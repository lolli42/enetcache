<?php
/***************************************************************
 * 
 *  Copyright notice
 *
 *  (c) 2009 Michael Knabe <mk@e-netconsulting.de>
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
	 * @param string Name of the table of current record
	 * @param array All fields that where changed for the record
	 * @param integer uid of record
	 * @return array An array with arrays containing uid and table as keys with the coresponding values 
	 */
	public static function findReferedDatabaseEntries($table, $fields, $id) {
			// Get all available fields and values
		$fields = self::getAllFields($table, $fields, $id);

			// Load TCA of table
		if (!$GLOBALS['TCA'][$table]['colums']) {
			t3lib_div::loadTCA($table);
		}

		$result = array();
		foreach ($GLOBALS['TCA'][$table]['columns'] as $localFieldName => $field) {
			$config = $field['config'];
			if (self::isReferenceField($config)) {
					// Handle reference fields
				if ($config['MM']) {
						// @TODO: Rename query to result, rename res to row(s)
						// @TODO: Sanitize tablenames, might not always given
					$query = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_foreign, tablenames', $config['MM'], 'uid_local='.$id);
					while($res = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($query)) {
						$tableName = self::getTableNameFromConfig($config, $res['tablenames']);
						$result[] = $tableName . '_' . $res['uid_foreign'];
					}
				} else {
					foreach (t3lib_div::trimExplode(',', $fields[$localFieldName], 1) as $uid) {
						if (!ctype_digit($uid)) {
							$uidArray = t3lib_div::revExplode('_', $fields['localFieldName']);
							$uid = $uidArray[1];
						} else {
							$tableName = self::getTableNameFromConfig($config, $uidArray[0]);
							$result[] = $tableName . '_' . $uid;
						}
					}
				}
			}
		}

		return $result;
	}


	/**
	 * Returns TRUE if the TCA/columns field type is a DB reference field
	 *
	 * @param array Config array for TCA/columns field
	 * @return boolean TRUE if DB reference field (group/db or select with foreign-table)
	 */
	public static function isReferenceField($conf) {
		return (
			($conf['type'] == 'group' && $conf['internal_type'] == 'db')
			|| ($conf['type'] == 'select' && $conf['foreign_table'])
		);
	}


	/**
	 * Returns the name of referenced table, which can be either in foreign_table or in allowed
	 *
	 * @param unknown_type $config
	 */
	public static function getTableNameFromConfig($config, $defaultTableName=0) {
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
	public static function getAllFields($table, $fields, $id) {
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $table, 'uid=' . $id);
		$row = (array)$GLOBALS['TYPO3_DB']->sql_fetch_assoc($result);

		return array_merge($row, $fields);
	}
}

?>
