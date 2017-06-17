<?php
namespace Lolli\Enetcache;

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

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\AbstractFrontend;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Provides a simple API to manage caching of plugin content elements.
 *
 * Main API class for extensions.
 */
class PluginCache implements SingletonInterface
{

    /**
     * @var AbstractFrontend Plugin content element cache frontend instance
     */
    protected $contentCache = null;

    /**
     * @var AbstractFrontend Page cache frontend instance
     */
    protected $pageCache = null;

    /**
     * @var array Hook objects
     */
    protected $hookObjects = [];

    /**
     * @var bool true if no_cache is set on this page
     */
    protected $noCache = false;

    /**
     * @var int default lifetime of content element cache entries
     */
    protected $defaultLifetime = 86400;

    /**
     * Initialize the content cache
     */
    public function __construct()
    {
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['enetcache']);

        // Set default element lifetime from extension config
        $this->setDefaultLifetime($extConf['defaultLifetime']);

        // Set cache instances for element and page cache
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $this->contentCache = $cacheManager->getCache('enetcachecontent');
        $this->pageCache = $cacheManager->getCache('cache_pages');

        // Initialize hook objects
        $this->initHooks();

        // Look if page has been set to no_cache
        $this->noCache = $GLOBALS['TSFE']->no_cache;
    }

    /**
     * Delete all content element cache entries. This does not clear the page cache!
     * You probably don't want to use this method, as it does not clear the page cache.
     * It's just here to be used by the BE-Button "Clear plugin cache".
     */
    public function flush()
    {
        // Call flush hook
        foreach ($this->hookObjects as $obj) {
            $obj->flush();
        }
        $this->contentCache->flush();
    }

    /**
     * Get a cache entry from cache.
     * Call this method at the beginning of your extension right after your identifier array has been calculated.
     * If a cache entry with this identifier exists the content will be returned, otherwise false
     * Make sure set() uses the same $identifier later on.
     *
     * Example usage: GeneralUtility::makeInstance(PluginCache::class)->set(array($this->piVars, $this->conf));
     *
     * @param array $identifiers Data that is used to identify this unique content element. I.e. piVars and fe_users group
     * @param bool $respectNoCache Weather to respect the no_cache parameter. Defaults to true: If no_cache is set, cache entry will not be fetched
     * @return mixed Cache data if found, otherwise false
     */
    public function get(array $identifiers, $respectNoCache = true)
    {
        // Caches are neither read nor written when no_cache is set.
        // Do an early return if so.
        if ($this->noCache && $respectNoCache) {
            return false;
        }

        // Build identifier hash
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
            // Return false in case no content cache was found with this identifier
            $result = false;
        }

        // Call get() post hook
        $hookParameters = [
            'identifiers' => $identifiers,
            'cacheData' => $cacheData,
        ];
        foreach ($this->hookObjects as $obj) {
            $obj->get($hookParameters);
        }

        return $result;
    }

    /**
     * Set a cache entry.
     * Call this method at the end of your plugin in order to add it to the cache.
     * Be sure $identifier is the same as in your get() request
     *
     * Example usage: return GeneralUtility::makeInstance(FrontendCache::class)->set([$this->piVars, $this->conf], $content, $tags, '1800');
     *
     * @param array $identifiers Data that is used to identify this content element. I.e. the piVars and the fe_users group
     * @param mixed $data content that is added to the cache, usually a string but could be an array as well
     * @param array $tags List of tags. Usually contains "tablename_uid" of all records used in your cache entry.
     * @param int $lifetime Lifetime of cache entry, will default to $this->defaultLifetime if not set
     * @param bool $respectNoCache Weather to respect the no_cache parameter. Defaults to true: If no_cache is set, cache entry will not be set
     * @return string $data for direct return after set()
     */
    public function set(array $identifiers, $data, array $tags = [], $lifetime = null, $respectNoCache = true)
    {
        // Caches are neither read nor written when no_cache is set.
        // Do an early return if so.
        if ($this->noCache && $respectNoCache) {
            return $data;
        }

        // Build identifier hash
        $hash = $this->calculateIdentifiersHash($identifiers);

        // Set lifetime of content element to default lifetime if lifetime is null
        if ($lifetime === null) {
            $lifetime = $this->getDefaultLifetime();
        }

        // Call set() hook
        $hookParameters = [
            'identifiers' => $identifiers,
            'data' => $data,
            // By reference: Hooks are allowed to change tags
            'tags' => &$tags,
            'lifetime' => $lifetime,
            'hash' => $hash,
        ];
        foreach ($this->hookObjects as $obj) {
            $obj->set($hookParameters);
        }

        // Add all tags of this element to page cache entry
        $this->addTagsToPageCache($tags);

        $cacheData = [
            'data' => $data,
            'tags' => $tags,
            'lifetime' => $lifetime,
            'endtime' => (time() + $lifetime),
        ];
        $this->setCachePageLifetime($lifetime);
        $this->contentCache->set($hash, serialize($cacheData), $tags, (int)$lifetime);

        return $data;
    }

    /**
     * Drops all cache entries tagged with given tags.
     * Depending on your configuration, this will drop multiple caches.
     * Defaults are cache_pages and enetcachecontent.
     * So if you drop a tag, your content element elements with this tags and all
     * of their page cache entries will be invalid.
     * It's no problem if a tag with this name doesn't exist
     *
     * Example usage: GeneralUtility::makeInstance(PluginCache::class)->drop(array('tt_news_4711'));
     *
     * @param array $tags List of tags
     */
    public function drop(array $tags)
    {
        // Call drop hook
        $hookParameters = [
            'tags' => $tags,
        ];
        foreach ($this->hookObjects as $obj) {
            $obj->drop($hookParameters);
        }

        // Iterate through to be dropped caches and flush entries by tag array
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['enetcache']['TAG_CACHES'] as $cache) {
            $cacheFE = GeneralUtility::makeInstance(CacheManager::class)->getCache($cache);
            foreach ($tags as $tag) {
                $cacheFE->flushByTag($tag);
            }
        }
    }

    /**
     * Hashes the serialized identifiers array
     *
     * @param array $identifiers identifiers
     * @return string hash value
     */
    protected function calculateIdentifiersHash($identifiers = [])
    {
        return md5(serialize($identifiers));
    }

    /**
     * Sets lifetime of the page cache entry.
     * It will only set the value if it's smaller than the current lifetime.
     * The page cache lifetime will be as long as the shortest content element lifetime
     *
     * @param int $lifetime Lifetime of content element
     */
    protected function setCachePageLifetime($lifetime)
    {
        if (!$GLOBALS['TSFE'] instanceof TypoScriptFrontendController) {
            return;
        }

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
     * @param array $tags Tags of this element
     */
    protected function addTagsToPageCache(array $tags)
    {
        if (!$GLOBALS['TSFE'] instanceof TypoScriptFrontendController) {
            return;
        }
        $GLOBALS['TSFE']->addCacheTags($tags);
    }

    /**
     * Set default lifetime
     *
     * @param int $lifetime Default lifetime
     */
    protected function setDefaultLifetime($lifetime)
    {
        $lifetime = intval($lifetime);
        if ($lifetime > 0) {
            $this->defaultLifetime = $lifetime;
        }
    }

    /**
     * Get default lifetime
     *
     * @return int lifetime
     */
    protected function getDefaultLifetime()
    {
        return $this->defaultLifetime;
    }

    /**
     * Initialize configured hook classes
     */
    protected function initHooks()
    {
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['enetcache']['hooks']['tx_enetcache'] as $className) {
            /** @var PluginCacheHookInterface $hookInstance */
            $hookInstance = GeneralUtility::makeInstance($className);
            $this->registerHook($hookInstance);
        }
    }

    /**
     * Register a hook instance to class array
     *
     * @param PluginCacheHookInterface $hook Hook classes must implement this interface
     */
    protected function registerHook(PluginCacheHookInterface $hook)
    {
        $this->hookObjects[] = $hook;
    }
}
