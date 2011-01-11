<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2011 Christian Kuhn <lolli@schwarzbu.ch>
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
 * Abstract enetcache class as wrapper for pi classes of thrird party extensions
 *
 * @package TYPO3
 * @subpackage enetcache
 */
abstract class tx_enetcache_extensionwrappers_pi_abstract extends tslib_pibase {

	/**
	 * @var array Plugin config array
	 */
	public $conf = array();

	/**
	 * @var integer Default lifetime 1 hour if not set
	 */
	protected $defaultCacheLifetime = 3600;

	/**
	 * Main method. It returns HTML content of plugin
	 *
	 * @param string Plugin content
	 * @param array Plugin configuration
	 * @return string HTML content
	 */
	public function main($content, $conf) {
		$this->init($conf);
		return $this->getContent();
	}

	/**
	 * Additional initalization plugin
	 *
	 * @return void
	 */
	protected function init($conf) {
		$this->conf = $conf;
		$this->pi_initPIflexForm();
	}

	/**
	 * Get content of plugin from cache, or render content and set to cache
	 *
	 * @return string HTML content of plugin
	 */
	protected function getContent() {
		if ($content = $this->getCachedContent()) {
			$content = $this->successfulCacheGetAction($content);
			return $content;
		}
		$content = $this->getNewContent();
		$cacheEntry = $this->manipulateCacheEntry($content);
		$this->setContentToCache($cacheEntry);
		return $content;
	}

	/**
	 * Get content from cache by indentifier
	 *
	 * @return mixed Content string on successfull get, FALSE on cache miss
	 */
	protected function getCachedContent() {
		return t3lib_div::makeInstance('tx_enetcache')->get($this->getCacheIdentifier());
	}

	/**
	 * Actions to be calculated right after successul cache get
	 *
	 * @param mixed cache entry
	 * @param string HTML content
	 */
	protected function successfulCacheGetAction($cacheEntry) {
		return $cacheEntry;
	}

	/**
	 * Instantiates and renders tag cloud content
	 *
	 * @return string HTML content
	 */
	protected function getNewContent() {
		$pluginObject = $this->instantiateObject();
		$pluginObject = $this->initializeObject($pluginObject);
		return $this->renderContent($pluginObject);
	}

	/**
	 * Set content to cache
	 *
	 * @param string HTML content
	 * @return string HTML content
	 */
	protected function setContentToCache($content) {
		return t3lib_div::makeInstance('tx_enetcache')->set(
			$this->getCacheIdentifier(),
			$content,
			$this->getCacheTags(),
			$this->getCacheLifetime()
		);
	}

	/**
	 * Actions to be done before setting calculated content to cache
	 *
	 * @param string HTML content
	 * @param mixed cache entry
	 */
	protected function manipulateCacheEntry($content) {
		return $content;
	}

	/**
	 * Instantiate object
	 * Must be implemented by extending classes
	 */
	abstract protected function instantiateObject();

	/**
	 * Initialize plugin object
	 *
	 * @param Instance of plugin
	 * @return Initialized plugin
	 */
	protected function initializeObject($pluginObject) {
		$pluginObject->cObj = &$this->cObj;
		return $pluginObject;
	}

	/**
	 * Render plugin content
	 *
	 * @param Fully initialized plugin object
	 * @return string HTML content
	 */
	protected function renderContent($pluginObject) {
		return $pluginObject->main('', $this->conf);
	}

	/**
	 * Get identifier of cache entry
	 *
	 * @return array Identifier parameters
	 */
	protected function getCacheIdentifier() {
		$cacheIdentifier = array();
		$cacheIdentifier[] = $this->conf;
		$cacheIdentifier[] = $this->cObj->data['pi_flexform']['data'];
		return $cacheIdentifier;
	}

	/**
	 * Cache tags of this cache entry
	 *
	 * @return array Cache tags
	 */
	protected function getCacheTags() {
			// No tags used for now
			// Overwrite this method in an own class to add own tagging
		return array();
	}

	/**
	 * Return lifetime if given by TS, else default
	 * Lifetime can be set via TS:
	 * plugin.tx_myplugin_piN.cachetime = 1234
	 *
	 * @return integer Lifetime of cache entry in seconds
	 */
	protected function getCacheLifetime() {
		if ((int) $this->conf['cachetime'] > 0) {
			return (int) $this->conf['cachetime'];
		}
		return $this->defaultCacheLifetime;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/classes/extensionwrappers/class.tx_enetcache_extensionwrappers_pi_abstract.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/classes/extensionwrappers/class.tx_enetcache_extensionwrappers_pi_abstract.php']);
}

?>
