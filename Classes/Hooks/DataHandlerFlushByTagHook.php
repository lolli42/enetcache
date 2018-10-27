<?php
namespace Lolli\Enetcache\Hooks;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Lolli\Enetcache\PluginCache;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Hooks to DataHandler for automagic cache clearing by tags
 */
class DataHandlerFlushByTagHook
{

    /**
     * Call enetcache and clear cache entries tagged with this item:
     * - table_name
     * - table_name_command (command one of move, copy, localize, delete, undelete)
     * - table_name_uid
     *
     * This hook is called for example by list module if deleting a record.
     * At this point the action is not yet done, so for example deleted is still 0.
     *
     * @param string $command new, delete, ...
     * @param string $table Table we are working on
     * @param int $id Record uid
     */
    public function processCmdmap_preProcess($command, $table, $id)
    {
        $tagsToDrop = [
            // This table name (tt_news)
            $table,
            // Table name with command name (tt_news_new)
            $table . '_' . $command,
            // Table name with id (tt_news_4711)
            $table . '_' . $id,
        ];

        // Handle referenced fields (mm relations and stuff)
        $isIdInteger = MathUtility::canBeInterpretedAsInteger($id);
        if (strlen($table) > 0 && $isIdInteger) {
            $tagsToDrop = array_merge($tagsToDrop, $this->findReferencedDatabaseEntries($table, [], $id));
        }

        $this->dropCacheTags($tagsToDrop);
    }

    /**
     * Forward dropping of referenced table records.
     *
     * Warning: This hook takes care to find relations of existing records (status updated & friends)
     * for relations which are deleted afterwards.
     *
     * @param array $fieldArray Changed fields
     * @param string $table The table we are working on
     * @param int $id Uid of record
     */
    public function processDatamap_preProcessFieldArray(array $fieldArray, $table, $id)
    {
        $isIdInteger = MathUtility::canBeInterpretedAsInteger($id);
        if (strlen($table) > 0 && $isIdInteger) {
            $tagsToDrop = $this->findReferencedDatabaseEntries($table, $fieldArray, $id);
            if (count($tagsToDrop) > 0) {
                $this->dropCacheTags($tagsToDrop);
            }
        }
    }

    /**
     * Forward dropping of referenced table records.
     *
     * Warning: At this point new mm relations are _not_ already written _if_ this is a _newN record,
     * so this hook will drop all tags to relations which where connected _before_ changing the record.
     * Otherwise dropping of old relations is handled in preProcess hook above.
     *
     * Example: A tt_news record was added (new id 4711), and the record was in category with uid 4.
     * tt_news_cat_4 tag will be dropped. This is usefull for new records. The tt_news list will not have the tag tt_news_4711,
     * but it will have tt_news_cat_4 tag, so the list will be rendered again on next page access.
     *
     * @param string $status new, delete, ...
     * @param string $table The table we are working on
     * @param int $id Uid of record
     * @param array $fieldArray Changed fields
     * @param DataHandler $pObj Unused reference to parent object
     */
    public function processDatamap_postProcessFieldArray($status, $table, $id, array $fieldArray, &$pObj)
    {
        $tagsToDrop = [];
        $tagsToDrop[] = $table;
        $tagsToDrop[] = $table . '_' . $status;

        // Substitute NEW with real ID
        if (GeneralUtility::isFirstPartOfStr($id, 'NEW')) {
            $id = $pObj->substNEWwithIDs[$id];
        }

        if (strlen($table) > 0 && intval($id) > 0) {
            $tagsToDrop[] = $table . '_' . $id;
            $tagsFromReferences = $this->findReferencedDatabaseEntries($table, $fieldArray, $id);
            $tagsToDrop = array_merge($tagsToDrop, $tagsFromReferences);
        }

        $this->dropCacheTags($tagsToDrop);
    }

    /**
     * Forward dropping of referenced table records - after new relations where written.
     * This is basically the same for finding referenced records as in postProcessFieldArray,
     * but now for _new_ relations of _new_ records
     *
     * @param DataHandler $pObj Unused reference to parent object
     */
    public function processDatamap_afterAllOperations(&$pObj)
    {
        if (count($pObj->dbAnalysisStore) > 0) {
            $table = $pObj->dbAnalysisStore[0][4];

            // Substitute NEW with real ID
            $id = $pObj->dbAnalysisStore[0][2];
            if (GeneralUtility::isFirstPartOfStr($id, 'NEW')) {
                $id = $pObj->substNEWwithIDs[$id];
            }

            $tagsFromReferences = [];
            if (strlen($table) > 0 && intval($id) > 0) {
                $tagsFromReferences = $this->findReferencedDatabaseEntries($table, [], $id);
            }

            if (count($tagsFromReferences) > 0) {
                $this->dropCacheTags($tagsFromReferences);
            }
        }
    }

    /**
     * Call enetcache to drop tags
     *
     * @param array $tags Tags to drop
     */
    protected function dropCacheTags(array $tags)
    {
        GeneralUtility::makeInstance(PluginCache::class)->drop($tags);
    }

    /**
     * Returns an array containing all database records that are referenced by a given record
     *
     * @param string $table Name of the table of current record
     * @param array $fields All fields that where changed for the record
     * @param int $id Uid of record
     * @return array An array with arrays containing uid and table as keys with the coresponding values
     */
    protected function findReferencedDatabaseEntries($table, array $fields, $id)
    {
        // Get all available fields and values
        $fields = $this->getAllFields($table, $fields, $id);

        $result = [];
        $referenceFields = $this->findReferenceFieldsForTable($table);
        foreach ($referenceFields as $localFieldName => $config) {
            // Handle reference fields
            if (isset($config['MM']) && $config['MM']) {
                $result = array_merge(
                    $result,
                    $this->findReferencedRowsForMMField($id, $config)
                );
            } else {
                $result = array_merge(
                    $result,
                    $this->findReferencedRowsForCSVField($fields[$localFieldName], $config)
                );
            }
        }
        return $result;
    }

    /**
     * Gets a list of records referred by the given mm-table
     *
     * @param int $uidLocal The UID of the local record
     * @param array $fieldConfig The configuration of the mm relation from TCA
     * @return array The referenced records in the form $tableName_$uid
     */
    protected function findReferencedRowsForMMField($uidLocal, array $fieldConfig)
    {
        $result = [];

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($fieldConfig['MM']);
        $queryBuilder->getRestrictions()->removeAll();
        $rows = $queryBuilder->select('*')
            ->from($fieldConfig['MM'])
            ->where(
                $queryBuilder->expr()->eq('uid_local', $queryBuilder->createNamedParameter($uidLocal, \PDO::PARAM_INT))
            )
            ->execute()
            ->fetchAll();
        foreach ($rows as $row) {
            if (!isset($row['tablenames'])) {
                $row['tablenames'] = 0;
            }
            $tableName = $this->getTableNameFromConfig($fieldConfig, $row['tablenames']);
            $result[] = $tableName . '_' . $row['uid_foreign'];
        }

        return $result;
    }

    /**
     * Fetches the referenced records from a field containing a csv-list of reverenced records
     * (Like TYPO3 uses when you don't use an MM-Table)
     *
     * @param string $fieldValue The value the db field contains
     * @param array $fieldConfig The TCS-configuration for the column/field
     * @return array A list of referenced fields in the form array('table_uid', 'table2_uid2', ...)
     */
    protected function findReferencedRowsForCSVField($fieldValue, array $fieldConfig)
    {
        $result = [];
        // If there is no mm table, the reference field is a comma separated list
        foreach (GeneralUtility::trimExplode(',', $fieldValue, true) as $uid) {
            if (!ctype_digit($uid)) {
                // It is a list of tablename_uid (foo_23,bar_42,foo_216)
                $uidArray = GeneralUtility::revExplode('_', $uid, 2);
                $uid = $uidArray[1];
                $tableName = $uidArray[0];
            } else {
                // Check this relation is not 0 (default's in table field)
                if (intval($uid) === 0) {
                    continue;
                }
                // It is a list of ints (23,42,216)
                $tableName = $this->getTableNameFromConfig($fieldConfig);
            }
            $result[] = $tableName . '_' . $uid;
        }
        return $result;
    }

    /**
     * Gets an array of all fields that contain references from $table
     * Key is the local field name and value is the TCA-config for that field
     *
     * @param string $table The name of the referring table
     * @return array The configuration of the fields
     */
    protected function findReferenceFieldsForTable($table)
    {
        $result = [];
        foreach ($GLOBALS['TCA'][$table]['columns'] as $localFieldName => $field) {
            $config = $field['config'];
            if ($this->isReferenceField($config)) {
                $result[$localFieldName] = $config;
            }
        }
        return $result;
    }

    /**
     * Returns true if the TCA/columns field type is a DB reference field
     *
     * @param array $conf Config array for TCA/columns field
     * @return bool true if DB reference field (group/db or select with foreign-table)
     */
    protected function isReferenceField(array $conf)
    {
        return
            ($conf['type'] == 'group' && isset($conf['internal_type']) && $conf['internal_type'] == 'db')
            || ($conf['type'] == 'select' && isset($conf['foreign_table']) && $conf['foreign_table']);
    }

    /**
     * Returns the name of referenced table, which can be either in foreign_table or in allowed
     *
     * @param array $config Field configuration
     * @param bool $defaultTableName Default table name
     * @return string Name of referenced table
     */
    protected function getTableNameFromConfig(array $config, $defaultTableName = false)
    {
        if ($defaultTableName) {
            $result = $defaultTableName;
        } elseif (isset($config['foreign_table']) && $config['foreign_table']) {
            $result = $config['foreign_table'];
        } else {
            $result = $config['allowed'];
        }

        return $result;
    }

    /**
     * Get all field values of this record
     *
     * @param string $table Table to find fields for
     * @param array $fields Changed fields
     * @param int $id uid of entry
     * @return array All fields given, plus the fields that have not changed
     */
    protected function getAllFields($table, array $fields, $id)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();
        $row = $queryBuilder->select('*')
            ->from($table)
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)))
            ->execute()
            ->fetch();
        return array_merge($row, $fields);
    }
}
