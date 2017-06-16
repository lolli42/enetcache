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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Add additional method to clear plugin cache on click on "Clear all caches" in top toolbar
 * Add additional method to clear plugin cache on clear all cache event
 */
class BackendContentCacheMethods {

	/**
	 * Flush (empty) cache backend of enetcache (eg. db table or memcached)
	 * Helper method called by "Clear plugin cache" flash icon
	 *
	 * @return void
	 */
	public function clearContentCache() {
		GeneralUtility::makeInstance(PluginCache::class)->flush();
	}
}