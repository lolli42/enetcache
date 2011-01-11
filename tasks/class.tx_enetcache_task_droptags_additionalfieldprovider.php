<?php
/***************************************************************
*  Copyright notice
*  (c) 2009-2011 Christian Kuhn <lolli@schwarzbu.ch>
*  (c) 2010-2011 Markus Guenther <markus.guenther@e-netconsulting.com>
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
 * Add an additional text input field for drop-by-tags task, to gain tags to be dropped.
 *
 * @author Markus Guenther <markus.guenther@e-netconsulting.com>
 * @package TYPO3
 * @subpackage enetcache
 */
class tx_enetcache_task_DropTags_AdditionalFieldProvider implements tx_scheduler_AdditionalFieldProvider {
	/**
	 * Add a Textfield
	 *
	 * @param	array					$taskInfo: reference to the array containing the info used in the add/edit form
	 * @param	object					$task: when editing, reference to the current task object. Null when adding.
	 * @param	tx_scheduler_Module		$parentObject: reference to the calling object (Scheduler's BE module)
	 * @return	array					Array containg all the information pertaining to the additional fields
	 *									The array is multidimensional, keyed to the task class name and each field's id
	 *									For each field it provides an associative sub-array with the following:
	 *										['code']		=> The HTML code for the field
	 *										['label']		=> The label of the field (possibly localized)
	 *										['cshKey']		=> The CSH key for the field
	 *										['cshLabel']	=> The code of the CSH label
	 */
	public function getAdditionalFields(array &$taskInfo, $task, tx_scheduler_Module $parentObject) {
			// Initialize selected feeds field value
		if (empty($taskInfo['tags'])) {
			if ($parentObject->CMD == 'edit') {
					// In case of edit, set to internal value if no data was submitted already
				$taskInfo['tags'] = implode(',', $task->tags);
			} else {
					// Otherwise set an empty value, as it will not be used anyway
				$taskInfo['tags'] = '';
			}
		}

		// Write the code for the field
		$fieldID = 'enetcache_task_droptags';
		$fieldCode = '<textarea rows="5" cols="35" name="tx_scheduler[tags]" id="' . $fieldID . '" class="wide" >' . $taskInfo['tags'] . '</textarea>';
		$additionalFields = array();
		$additionalFields[$fieldID] = array(
			'code' => $fieldCode,
			'label' => 'LLL:EXT:enetcache/locallang.xml:scheduler.droptags.tagList',
			'cshKey' => '_MOD_tools_txschedulerM1',
			'cshLabel' => $fieldID
		);

		return $additionalFields;
	}


	/**
	 * This method checks any additional data that is relevant to the specific task
	 * If the task class is not relevant, the method is expected to return true
	 *
	 * @param	array					$submittedData: reference to the array containing the data submitted by the user
	 * @param	tx_scheduler_Module		$parentObject: reference to the calling object (Scheduler's BE module)
	 * @return	boolean					True if validation was ok (or selected class is not relevant), false otherwise
	 */
	public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $parentObject) {
		$tags = trim($submittedData['tags']);
		$isValid = $this->isValidTagList(explode(',', $tags));
		if (!$isValid) {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:enetcache/locallang.xml:scheduler.droptags.invalidTagList'), t3lib_FlashMessage::ERROR);
		}
		return $isValid;
	}

	/**
	 * This method is used to save any additional input into the current task object
	 * if the task class matches
	 *
	 * @param	array				$submittedData: array containing the data submitted by the user
	 * @param	tx_scheduler_Task	$task: reference to the current task object
	 * @return	void
	 */
	public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
		$tags = trim($submittedData['tags']);
		$task->tags = explode(',', $tags);
	}

	/**
	 * Sanitize tag list
	 *
	 * @param array Tag list
	 * @return boolean TRUE if tag list validates
	 */
	protected function isValidTagList(array $tags = array()) {
		$isValid = TRUE;
		foreach ($tags as $tag) {
			if (!preg_match(t3lib_cache_frontend_Frontend::PATTERN_TAG, $tag)) {
				$isValid = FALSE;
			}
		}

		return $isValid;
	}
} // End of class

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/tasks/class.tx_enetcache_task_droptags_additionalfieldprovider.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/tasks/class.tx_enetcache_task_droptags_additionalfieldprovider.php']);
}

?>
