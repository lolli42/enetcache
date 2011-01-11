<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009-2010 Christian Kuhn <lolli@schwarzbu.ch>
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
 * Collect garbage of cache backends.
 * This iterates through configured cache framework backends and call the garbage collection
 *
 * @obsolete Since TYPO3 version 4.5
 *
 * @author		Christian Kuhn <lolli@schwarzbu.ch>
 * @package		TYPO3
 * @subpackage	enetcache
 */
class tx_enetcache_gccachebackends extends tx_scheduler_Task {
	/**
	 * Selected backends to do garbage collection for.
	 * Feeded by additional field provider
	 *
	 * @var array Selected backends to do garbage collection for
	 */
	public $selectedBackends = array();

	/**
	 * Method called by scheduler
	 *
	 * @return void
	 */
	public function execute() {
		$cacheConfigurations = $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'];

		$cacheConfigurations = $this->fixExtbaseCacheConfiguration($cacheConfigurations);

			// Iterate through configured cache configurations and call garbageCollection if
			// backend is within selected backends in additonal fields of task
		foreach ($cacheConfigurations as $cacheName => $cacheConfiguration) {
			$cacheBackend = array($cacheConfiguration['backend']);
			$collectGarbageOfBackend = array_intersect($cacheBackend, $this->selectedBackends);
			if (count($collectGarbageOfBackend) === 1) {
				try {
					$cache = $GLOBALS['typo3CacheManager']->getCache($cacheName);
				} catch (t3lib_cache_exception_NoSuchCache $exception) {
					$GLOBALS['typo3CacheFactory']->create(
						$cacheName,
						$cacheConfiguration['frontend'],
						$cacheConfiguration['backend'],
						$cacheConfiguration['options']
					);
					$cache = $GLOBALS['typo3CacheManager']->getCache($cacheName);
				}
				$cache->collectGarbage();
			}
		}

		$success = TRUE;
		return($success);
	}

	/**
	 * Hack to fix "Class not found" issue if extbase is installed
	 * This was fixed in extbase rev. 2320, for 1.2.0beta3 (included in 4.4.0)
	 *
	 * This method will only do something if extbase is loaded and TYPO3
	 * version is smaller than 4.3.0 - 4.3.999
	 *
	 * @see http://forge.typo3.org/issues/show/7968
	 * @see http://forge.typo3.org/issues/8094
	 * @deprecated Method will be removed if version requirement of enetcache is raised to 4.4
	 */
	protected function fixExtbaseCacheConfiguration($cacheConfigurations) {
		if (t3lib_extMgm::isloaded('extbase') && (t3lib_div::int_from_ver(TYPO3_version) < 4030999)) {
			$cacheConfigurations['cache_extbase_reflection']['frontend'] = 't3lib_cache_frontend_VariableFrontend';
		}
		return $cacheConfigurations;
	}
} // End of class

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/tasks/class.tx_enetcache_gccachebackends.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/tasks/class.tx_enetcache_gccachebackends.php']);
}

?>
