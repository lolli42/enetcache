<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2011 Christian Kuhn (lolli@schwarzbu.ch)
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
 * Hooks to TCEmain for automagic cache clearing by tags
 *
 * @author  Michael Knabe <mk@e-netconsulting.de>
 * @author	Christian Kuhn <lolli@schwarzbu.ch>
 * @package TYPO3
 * @subpackage enetcache
 */
class tx_enetcache_tcemain {

	/**
	 * Call enetcache and clear cache entries tagged with this item:
	 * - table_name
	 * - table_name_command (command one of move, copy, localize, delete, undelete)
	 * - table_name_uid
	 *
	 * This hook is called for example by list module if deleting a record.
	 * At this point the action is not yet done, so for example deleted is still 0.
	 *
	 * @param string new, delete, ...
	 * @param string Table we are working on
	 * @param int Record uid
	 * @param mixed	Unused
	 * @param t3lib_TCEmain Unused reference to parent object
	 * @return	void
	 */
	public function processCmdmap_preProcess($command, $table, $id, $value, &$pObj) {
		$tagsToDrop = array(
				// This table name (tt_news)
			$table,
				// Table name with command name (tt_news_new)
			$table . '_' . $command,
				// Table name with id (tt_news_4711)
			$table . '_' . $id,
		);

			// Handle referenced fields (mm relations and stuff)
		if (strlen($table) > 0 && t3lib_div::testInt($id)) {
			$tagsToDrop = array_merge($tagsToDrop, tx_enetcache_tcaHandler::findReferedDatabaseEntries($table, array(), $id));
		}

		$this->dropCacheTags($tagsToDrop);
	}

	/**
	 * Forward dropping of referenced table records.
	 *
	 * Warning: This hook takes care to find relations of existing records (status updated & friends)
	 * for relations which are deleted afterwards.
	 *
	 * @param array Changed fields
	 * @param string The table we are working on
	 * @param integer Uid of record
	 * @param t3lib_TCEmain Unused reference to parent object
	 * @return void
	 */
	public function processDatamap_preProcessFieldArray($fieldArray, $table, $id, &$pObj) {
		if (strlen($table) > 0 && t3lib_div::testInt($id)) {
			$tagsToDrop = tx_enetcache_tcaHandler::findReferedDatabaseEntries($table, $fieldArray, $id);
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
	 * @param string new, delete, ...
	 * @param string The table we are working on
	 * @param integer Uid of record
	 * @param array Changed fields
	 * @param t3lib_TCEmain Unused reference to parent object
	 * @return void
	 */
	public function processDatamap_postProcessFieldArray($status, $table, $id, $fieldArray, &$pObj) {
		$tagsToDrop = array();
		$tagsToDrop[] = $table;
		$tagsToDrop[] = $table . '_' . $status;

			// Substitute NEW with real ID
		if (t3lib_div::isFirstPartOfStr($id, 'NEW')) {
			$id = $pObj->substNEWwithIDs[$id];
		}

		if (strlen($table) > 0 && intval($id) > 0) {
			$tagsToDrop[] = $table . '_' . $id;
			$tagsFromReferences = tx_enetcache_tcaHandler::findReferedDatabaseEntries($table, $fieldArray, $id);
			$tagsToDrop = array_merge($tagsToDrop, $tagsFromReferences);
		}

		$this->dropCacheTags($tagsToDrop);
	}

	/**
	 * Forward dropping of referenced table records - after new relations where written.
	 * This is basically the same for finding referenced records as in postProcessFieldArray,
	 * but now for _new_ relations of _new_ records
	 *
	 * @param t3lib_TCEmain Unused reference to parent object
	 * @return void
	 */
	public function processDatamap_afterAllOperations(&$pObj) {
		if (count($pObj->dbAnalysisStore) > 0) {
			$table = $pObj->dbAnalysisStore[0][4];

				// Substitute NEW with real ID
			$id = $pObj->dbAnalysisStore[0][2];
			if (t3lib_div::isFirstPartOfStr($id, 'NEW')) {
				$id = $pObj->substNEWwithIDs[$id];
			}

			if (strlen($table) > 0 && intval($id) > 0) {
				$tagsFromReferences = tx_enetcache_tcaHandler::findReferedDatabaseEntries($table, array(), $id);
			}

			if (count($tagsFromReferences) > 0) {
				$this->dropCacheTags($tagsFromReferences);
			}
		}
	}

	/**
	 * Call enetcache to drop tags
	 *
	 * @param array Tags to drop
	 * @return void
	 */
	protected function dropCacheTags(array $tags) {
		t3lib_div::makeInstance('tx_enetcache')->drop($tags);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/hooks/class.tx_enetcache_tcemain.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/hooks/class.tx_enetcache_tcemain.php']);
}
?>
