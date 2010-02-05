<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Christian Kuhn <lolli@schwarzbu.ch>
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
 * Abstract enetcache class as wrapper for pi classes of wec_map
 *
 * @package TYPO3
 * @subpackage enetcache
 * @depends wec_map
 */
abstract class tx_enetcache_extensionwrappers_wec_map_pi_abstract extends tx_enetcache_extensionwrappers_pi_abstract {
	/**
	 * wec_map sets two js files to $GLOBALS additionalHeaderData
	 * We cache those files next to the content before writing the calculated content to cache
	 *
	 * @param string HTML content
	 * @param mixed content
	 */
	protected function manipulateCacheEntry($content) {
		$cacheEntry = array();
		$cacheEntry['content'] = $content;
		$cacheEntry['additionalHeaderData'] = array();
		$cacheEntry['additionalHeaderData']['wec_map'] = $GLOBALS['TSFE']->additionalHeaderData['wec_map'];
		$cacheEntry['additionalHeaderData']['wec_map_googleMaps'] = $GLOBALS['TSFE']->additionalHeaderData['wec_map_googleMaps'];
		return $cacheEntry;
	}

	/**
	 * Set cached wec map header data to tsfe again
	 *
	 * @param array cache entry
	 * @return string HTML content
	 */
	protected function successfulCacheGetAction($cacheEntry) {
		$GLOBALS['TSFE']->additionalHeaderData['wec_map'] = $cacheEntry['additionalHeaderData']['wec_map'];
		$GLOBALS['TSFE']->additionalHeaderData['wec_map_googleMaps'] = $cacheEntry['additionalHeaderData']['wec_map_googleMaps'];
		return $cacheEntry['content'];
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/classes/extensionwrappers/class.tx_enetcache_extensionwrappers_wec_map_pi_abstract.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/classes/extensionwrappers/class.tx_enetcache_extensionwrappers_wec_map_pi_abstract.php']);
}

?>
