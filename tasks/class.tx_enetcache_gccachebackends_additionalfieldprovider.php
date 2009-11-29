<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Christian Kuhn <lolli@schwarzbu.ch>
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
 * Additional BE fields for cache backend garbage collection
 *
 * @author		Christian Kuhn <lolli@schwarzbu.ch>
 * @package		TYPO3
 * @subpackage	enetcache
 */
class tx_enetcache_gccachebackends_additionalfieldprovider implements tx_scheduler_AdditionalFieldProvider {
	/**
	 * Add a multiple select box for cache backends for which garbage collection should be called
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
			// Initialize selectedfeeds field value
		if (empty($taskInfo['selectedBackends'])) {
			if ($parentObject->CMD == 'add') {
			} elseif ($parentObject->CMD == 'edit') {
					// In case of edit, and editing a test task, set to internal value if no data was submitted already
				$taskInfo['selectedBackends'] = $task->selectedBackends;
			} else {
					// Otherwise set an empty value, as it will not be used anyway
				$taskInfo['selectedBackends'] = '';
			}
		}

		$fieldID = 'task_selectedBackends';
		$fieldCode = '<select name="tx_scheduler[selectedBackends][]" id="' . $fieldID .
			'" size="10" style="width: 250px;" multiple="multiple">' . $this->getCacheBackendOptions($taskInfo['selectedBackends']) .
			'</select>';
		$additionalFields[$fieldID] = array(
			'code'     => $fieldCode,
			'label'    => 'LLL:EXT:enetcache/locallang.xml:scheduler.gccachebackends.availableBackends',
		);

		return $additionalFields;
	}

	/**
	 * Build select options of available backends
	 *
	 * @param	array Selected backends
	 * @return	string HTML of selectorbox options
	 */
	protected function getCacheBackendOptions($selectedBackends) {
		$availableBackends = $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheBackends'];

		$options = array();
		foreach ($availableBackends as $backendName => $backendClass) {
			if (in_array($backendName, (array)$selectedBackends)) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			$options[] = '<option value="' . $backendName .  '"' . $selected .
				'>' . $backendName . '</option>';
		}

		return implode($options);
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
		return true;
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
		$task->selectedBackends = $submittedData['selectedBackends'];
	}
} // End of class

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/tasks/class.tx_enetcache_gccachebackends_additionalfieldprovider.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/tasks/class.tx_enetcache_gccachebackends_additionalfieldprovider.php']);
}

?>
