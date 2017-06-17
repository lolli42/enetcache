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
use Lolli\Enetcache\TcaHandler;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Hooks to DataHandler for automagic cache clearing by tags
 */
class DataHandlerFlushByTag
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
     * @param mixed $value Unused
     * @param DataHandler $pObj Unused reference to parent object
     * @return void
     */
    public function processCmdmap_preProcess($command, $table, $id, $value, &$pObj)
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
            $tagsToDrop = array_merge($tagsToDrop, TcaHandler::findReferedDatabaseEntries($table, [], $id));
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
     * @param DataHandler $pObj Unused reference to parent object
     * @return void
     */
    public function processDatamap_preProcessFieldArray(array $fieldArray, $table, $id, &$pObj)
    {
        $isIdInteger = MathUtility::canBeInterpretedAsInteger($id);
        if (strlen($table) > 0 && $isIdInteger) {
            $tagsToDrop = TcaHandler::findReferedDatabaseEntries($table, $fieldArray, $id);
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
     * @return void
     */
    public function processDatamap_postProcessFieldArray($status, $table, $id, array $fieldArray, &$pObj)
    {
        $tagsToDrop = array();
        $tagsToDrop[] = $table;
        $tagsToDrop[] = $table . '_' . $status;

        // Substitute NEW with real ID
        if (GeneralUtility::isFirstPartOfStr($id, 'NEW')) {
            $id = $pObj->substNEWwithIDs[$id];
        }

        if (strlen($table) > 0 && intval($id) > 0) {
            $tagsToDrop[] = $table . '_' . $id;
            $tagsFromReferences = TcaHandler::findReferedDatabaseEntries($table, $fieldArray, $id);
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
     * @return void
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
                $tagsFromReferences = TcaHandler::findReferedDatabaseEntries($table, array(), $id);
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
     * @return void
     */
    protected function dropCacheTags(array $tags)
    {
        GeneralUtility::makeInstance(PluginCache::class)->drop($tags);
    }
}
