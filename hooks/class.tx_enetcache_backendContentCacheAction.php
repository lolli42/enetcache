<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2013 Michael Knabe <mk@e-netconsulting.de>
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
 * Register a new item in top toolbar to clear plugin cache
 *
 * @author  Michael Knabe <mk@e-netconsulting.de>
 * @author  Christian Kuhn <lolli@schwarzbu.ch>
 */

// @deprecated With core versions < 6.0, the interface must be required manually
if (Tx_Enetcache_Utility_Compatibility::convertVersionNumberToInteger(TYPO3_version) <= '4007999' && !class_exists('backend_cacheActionsHook')) {
	require_once($GLOBALS['BACK_PATH'] . 'interfaces/interface.backend_cacheActionsHook.php');
}

class tx_enetcache_backendContentCacheAction implements backend_cacheActionsHook {
	/**
	 * Clear plugin cache item in top toolbar
	 * This hook method is called by the cache action (the flash in top toolbar) with click on "Clear plugin cache"
	 *
	 * @param array cacheAction
	 * @param array optionValues
	 * @return void
	 */
	public function manipulateCacheActions(&$cacheActions, &$optionValues) {
		$title = 'Clear plugin cache';
		$cacheActions[] = array(
			'id' => 'clearContentCache',
			'title' => $title,
			'href' => $GLOBALS['BACK_PATH'] . 'ajax.php?ajaxID=enetcache::clearContentCache',
			'icon' => t3lib_iconWorks::getSpriteIcon('extensions-enetcache-clearcontentcache', array('title' => $title)),
		);
		$optionValues[] = 'clearContentCache';
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/hooks/class.tx_enetcache_backendContentCacheAction.php'])  {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/hooks/class.tx_enetcache_backendContentCacheAction.php']);
}