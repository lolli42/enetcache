<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2011 Michael Knabe <mk@e-netconsulting.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * This class provides some features that are needed by the backend hooks
 * in order to find references between entries in the database
 *
 * @author Michael Knabe <mk@e-netconsulting.de>
 * @author  Christian Kuhn <lolli@schwarzbu.ch>
 * @package TYPO3
 * @subpackage enetcache
 * @see tx_enetcache_tcemain
 */
class tx_enetcache_tcaHandler {

	/**
	 * Returns an array containing all database records that are referenced by a given record
	 *
	 * @param string $table Name of the table of current record
	 * @param array $fields All fields that where changed for the record
	 * @param integer $id Uid of record
	 * @return array An array with arrays containing uid and table as keys with the coresponding values
	 */
	public static function findReferedDatabaseEntries($table, array $fields, $id) {
			// Get all available fields and values
		$fields = self::getAllFields($table, $fields, $id);

		self::loadTcaForTable($table);

		$result = array();
		$referenceFields = self::findReferenceFieldsForTable($table);
		foreach ($referenceFields as $localFieldName => $config) {
				// Handle reference fields
			if ($config['MM']) {
				$result = array_merge(
					$result,
					self::findReferencedRowsForMMField($id, $config)
				);
			} else {
				$result = array_merge(
					$result,
					self::findReferencedRowsForCSVField($fields[$localFieldName], $config)
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
	 * @return array The refered records in the form $tableName_$uid
	 */
	protected static function findReferencedRowsForMMField($uidLocal, array $fieldConfig) {
		$result = array();

		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			$fieldConfig['MM'],
			'uid_local=' . intval($uidLocal)
		);
		foreach ($rows as $row) {
			if (!isset($row['tablenames'])) {
				$row['tablenames'] = 0;
			}
			$tableName = self::getTableNameFromConfig($fieldConfig, $row['tablenames']);
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
	protected static function findReferencedRowsForCSVField($fieldValue, array $fieldConfig) {
		$result = array();
			// If there is no mm table, the reference field is a comma separated list
		foreach (t3lib_div::trimExplode(',', $fieldValue, TRUE) as $uid) {
			if (!ctype_digit($uid)) {
					// It is a list of tablename_uid (foo_23,bar_42,foo_216)
				$uidArray = t3lib_div::revExplode('_', $uid, 2);
				$uid = $uidArray[1];
				$tableName = $uidArray[0];
			} else {
					// Check this relation is not 0 (default's in table field)
				if (intval($uid) === 0) {
					continue;
				}
					// It is a list of ints (23,42,216)
				$tableName = self::getTableNameFromConfig($fieldConfig, $uidArray[0]);
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
	protected static function findReferenceFieldsForTable($table) {
		$result = array();
		foreach ($GLOBALS['TCA'][$table]['columns'] as $localFieldName => $field) {
			$config = $field['config'];
			if (self::isReferenceField($config)) {
				$result[$localFieldName] = $config;
			}
		}
		return $result;
	}
	
	
	/**
	 * Loads TCA configuration for the given table.
	 *
	 * @param string $table The table whose TCA shall be loaded
	 * @return void
	 */
	protected static function loadTcaForTable($table) {
		if (!$GLOBALS['TCA'][$table]['colums']) {
			t3lib_div::loadTCA($table);
		}
	}
	
	
	/**
	 * Returns TRUE if the TCA/columns field type is a DB reference field
	 *
	 * @param array Config array for TCA/columns field
	 * @return boolean TRUE if DB reference field (group/db or select with foreign-table)
	 */
	protected static function isReferenceField(array $conf) {
		return (
			($conf['type'] == 'group' && $conf['internal_type'] == 'db')
			|| ($conf['type'] == 'select' && $conf['foreign_table'])
		);
	}


	/**
	 * Returns the name of referenced table, which can be either in foreign_table or in allowed
	 *
	 * @param array $config Field configuration
	 * @param boolean $defaultTableName Default table name
	 */
	protected static function getTableNameFromConfig(array $config, $defaultTableName = FALSE) {
		if ($defaultTableName) {
			$result = $defaultTableName;
		} elseif ($config['foreign_table']) {
			$result = $config['foreign_table'];
		} else {
			$result = $config['allowed'];
		}

		return $result;
	}


	/**
	 * Get all field values of this record
	 *
	 * @param string Table to find fields for
	 * @param array Changed fields
	 * @param integer uid of entry
	 * @return array All fields given, plus the fields that have not changed
	 */
	protected static function getAllFields($table, array $fields, $id) {
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			$GLOBALS['TYPO3_DB']->quoteStr($table, $table),
			'uid=' . intval($id)
		);
		$row = (array)$GLOBALS['TYPO3_DB']->sql_fetch_assoc($result);
		return array_merge($row, $fields);
	}
}
?>
