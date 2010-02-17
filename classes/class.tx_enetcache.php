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
 * tx_enetcache
 *
 * Provides a simple api to manage the caching of plugin content elements
 *
 * @author  Michael Knabe <mk@e-netconsulting.de>
 * @author  Christian Kuhn <lolli@schwarzbu.ch>
 * @package TYPO3
 * @subpackage enetcache
 */
class tx_enetcache implements t3lib_Singleton {
	/**
	 * @const The extension key
	 */
	const EXTkey = enetcache;

	/**
	 * @var t3lib_cache_frontend_AbstractFrontend Plugin content element cache frontend instance
	 */
	protected $contentCache = NULL;

	/**
	 * @var t3lib_cache_frontend_AbstractFrontend Page cache frontend instance
	 */
	protected $pageCache = NULL;

	/**
	 * @var array Hook objects
	 */
	protected $hookObjects=array();

	/**
	 * @var boolean Wether or not no_cache is set on this page
	 */
	protected $noCache = FALSE;

	/**
	 * @var int default lifetime of content element cache entries
	 */
	protected $defaultLifetime = 86400;


	/**
	 * Initialize the content cache
	 * Don't call this directly with "new", use t3lib_div::makeInstance,
	 * which is aware that this class is a singleton.
	 *
	 * @return void
	 */
	public function __construct() {
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXTkey]);

			// Set default element lifetime from extension config
		$this->setDefaultLifetime($extConf['defaultLifetime']);

			// Create content cache instance
		$this->createContentCache();

			// Set cache instances for element and page cache
		$this->contentCache = $GLOBALS['typo3CacheManager']->getCache('cache_enetcache_contentcache');
		$this->pageCache = $GLOBALS['typo3CacheManager']->getCache('cache_pages');

			// Initialize hook objects
		$this->initHooks();

			// Look if page has been set to no_cache
		$this->noCache = $GLOBALS['TSFE']->no_cache;
	}


	/**
	 * Deletes all content elment cache entries. This does not clear the page cache!
	 * You probably don't want to use this method, as it does not clear the page cache.
	 * It's just here to be used by the BE-Button "Clear plugin cache".
	 *
	 * @return void
	 */
	public function flush() {
			// Call flush hook
		foreach($this->hookObjects as $obj) {
			$obj->flush();
		}
		$this->contentCache->flush();
	}


	/**
	 * Get a cache entry from cache.
	 * Call this method at the beginning of your extension right after your identifier array has been calculated.
	 * If a cache entry with this identifier exists the content will be returned, otherwise FALSE
	 * Make sure set() uses the same $identifier later on.
	 *
	 * Example usage: t3lib_div::makeInstance('tx_enetcache')->set(array($this->piVars, $this->conf));
	 *
	 * @param array	Data that is used to identify this unique content element. I.e. piVars and fe_users group
	 * @return mixed Cache data if found, otherwise FALSE
	 */
	public function get(array $identifiers) {
			// Caches are neither read nor written when no_cache is set.
			// Do an early return if so.
		if ($this->noCache) {
			return FALSE;
		}

			// Build hash tag
		$hash = $this->calculateIdentifiersHash($identifiers);

			// Get cache entry
		$cacheData = unserialize($this->contentCache->get($hash));

		if (is_array($cacheData)) {
				// Add all tags of this element to page cache entry
			$this->addTagsToPageCache($cacheData['tags']);

				// Calculate new page lifetime
				// This is especially important for element caches that are re-used for several pages
			if (array_key_exists('endtime', $cacheData)) {
				$this->setCachePageLifetime($cacheData['endtime'] - $GLOBALS['EXEC_TIME']);
			}
				// Assign our actual content.
				// This might also be an array with content and further information
			$result =  $cacheData['data'];
		} else {
				// Return FALSE in case no content cache was found with this identifier
			$result = FALSE;
		}

			// Call get() post hook
		$hookParameters = array (
			'identifiers' => $identifiers,
			'cacheData' => $cacheData,
		);
		foreach($this->hookObjects as $obj) {
			$obj->get($hookParameters);
		}

		return $result;
	}


	/**
	 * Set a cache entry.
	 * Call this method at the end of your plugin in order to add it to the cache.
	 * Be sure $identifier is the same as in your get() request
	 *
	 * Example usage: return t3lib_div::makeInstance('tx_enetcache')->set(array($this->piVars, $this->conf), $content, $tags, '1800');
	 *
	 * @param array Data that is used to identify this content element. I.e. the piVars and the fe_users group
	 * @param mixed content that is added to the cache, usually a string but could be an array as well
	 * @param array List of tags. Usually contains "tablename_uid" of all records used in your cache entry.
	 * @param int Lifetime of cache entry, will default to $this->defaultLifetime if not set
	 * @return string $data for direct return after set()
	 */
	public function set(array $identifiers, $data, array $tags = array(), $lifetime = null) {
			// Caches are neither read nor written when no_cache is set.
			// Do an early return if so.
		if ($this->noCache) {
			return $data;
		}

			// Build hash tag
		$hash = $this->calculateIdentifiersHash($identifiers);

			// Set lifetime of content element to default lifetime if not set
		if (!$lifetime) {
			$lifetime = $this->getDefaultLifetime();
		}

			// Call set() hook
		$hookParameters = array (
			'identifiers' => $identifiers,
			'data' => $data,
				// Hooks are allowed to change tags
			'tags' => &$tags,
			'lifetime' => $lifetime,
			'hash' => $hash,
		);
		foreach($this->hookObjects as $obj) {
			$obj->set($hookParameters);
		}

			// Add all tags of this element to page cache entry
		$this->addTagsToPageCache($tags);
		
		$cacheData = array(
			'data' => $data,
			'tags' => $tags,
			'lifetime' => $lifetime,
			'endtime' => (time() + $lifetime),
		);
		$this->setCachePageLifetime($lifetime);
		$this->contentCache->set($hash, serialize($cacheData), $tags, $lifetime);

		return $data;
	}

	
	/**
	 * Drops all cache entries tagged with given tags.
	 * Depending on your configuration, this will drop flush multiple caches.
	 * Defaults are cache_pages and cache_enetcache_contentcache.
	 * So if you drop a tag, your content element elements with this tags and all
	 * of their page cache entries will be invalid.
	 * It's no problem if a tag with this name doesn't exist
	 *
	 * Example usage: t3lib_div::makeInstance('tx_enetcache')->drop(array('tt_news_4711'));
	 *
	 * @param array List of tags
	 * @return void
	 */
	public function drop(array $tags) {
			// Call drop hook
		$hookParameters = array (
			'tags' => $tags,
		);
		foreach($this->hookObjects as $obj) {
			$obj->drop($hookParameters);
		}

			// Iterate through to be dropped caches and flush entries by tag array
		foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][self::EXTkey]['TAG_CACHES'] as $cache) {
			$cacheFE = $GLOBALS['typo3CacheManager']->getCache($cache);
			$cacheFE->flushByTags($tags);
		}
	}


	/**
	 * Hashes the serialized identifiers array
	 *
	 * @param array identifiers
	 * @return string hash value
	 */
	protected static function calculateIdentifiersHash($identifiers = array()) {
		return md5(serialize($identifiers));
	}


	/**
	 * Sets lifetime of the page cache entry.
	 * It will only set the value if it's smaller than the current lifetime.
	 * The page cache lifetime will be as long as the shortest content element lifetime
	 *
	 * @param integer Lifetime of content element
	 */
	protected function setCachePageLifetime($lifetime) {
			// 0 doesn't really mean 0 seconds but it tells TSFE to use the default configured elsewhere.
			// So we filter that out.
		if ($lifetime) {
			if (!$GLOBALS['TSFE']->page['cache_timeout']) {
					// No cache timeout was set yet.
					// This would cause min to always return 0 so we filter it out.
				$GLOBALS['TSFE']->page['cache_timeout'] = $lifetime;
			} else {
					// Set lifetime to lowest value of given or current lifetime
				$GLOBALS['TSFE']->page['cache_timeout'] = min($lifetime, $GLOBALS['TSFE']->page['cache_timeout']);
			}
		}
	}


	/**
	 * Add tags to page cache tags
	 * Every standard TYPO3 page cache entry has all tags of all content elements on this page
	 *
	 * @param array Tags of this element
	 * @return void
	 */
	protected function addTagsToPageCache(array $tags) {
		$GLOBALS['TSFE']->addCacheTags($tags);
	}


	/**
	 * Call cache factory to create a new cache instance for our element cache
	 *
	 * @return void
	 */
	protected function createContentCache() {
		t3lib_div::makeInstance('t3lib_cache_Factory')->create(
			'cache_enetcache_contentcache',
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache']['frontend'],
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache']['backend'],
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache']['options']
		);
	}


	/**
	 * Set default lifetime
	 *
	 * @param integer Default lifetime
	 * @return void
	 */
	protected function setDefaultLifetime($lifetime) {
		$lifetime = intval($lifetime);
		if ($lifetime > 0) {
			$this->defaultLifetime = $lifetime;
		}
	}


	/**
	 * Get default lifetime
	 *
	 * @return integer lifetime
	 */
	protected function getDefaultLifetime() {
		return $this->defaultLifetime;
	}


	/**
	 * Initialize configured hook classes
	 *
	 * @return void
	 */
	protected function initHooks() {
		foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['enetcache']['hooks']['tx_enetcache'] as $classname) {
			$this->registerHook(t3lib_div::makeInstance($classname));
		}
	}


	/**
	 * Register a hook instance to class array
	 *
	 * @param tx_enetcache_hookable Hook classes must implement this interface
	 * @return void
	 */
	protected function registerHook(tx_enetcache_hookable $hook) {
		$this->hookObjects[] = $hook;
	}
} // End of tx_enetcache

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/class.tx_enetcache.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/class.tx_enetcache.php']);
}

?>
