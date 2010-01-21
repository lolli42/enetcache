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
 * A cache implementation as stupid wrapper for vge_tagcloud
 *
 * @package TYPO3
 * @subpackage enetcache
 * @depends vge_tagcloud
 */
class tx_enetcache_extensionwrappers_vge_tagcloud_pi1 extends tslib_pibase {
	/**
	 * @var array Plugin config array
	 */
	public $conf = array();

	/**
	 * @var array Cache identifier
	 */
	protected $cacheIndentifier = FALSE;

	/**
	 * @var object tx_vgetagcloud_pi1
	 */
	protected $tagCloudObject = NULL;

	/**
	 * @var integer Default lifetime 1 hour if not set
	 */
	protected $defaultCacheLifetime = 3600;

	/**
	 * This is the main method of the plugin. It returns the content to display
	 *
	 * @param string Plugin content
	 * @param array Plugin configuration
	 * @return string HTML Content of vge_tagcloud_pi1
	 */
	public function main($content, $conf) {
		$this->init($conf);
		return $this->getTagCloudContent();
	}

	/**
	 * Additional initalization for this plugin
	 *
	 * @return void
	 */
	protected function init($conf) {
		$this->conf = $conf;
	}

	/**
	 * Get content from cache of vge_tagcloud, or render content and set to cache
	 *
	 * @return string HTML Content of vge_tagcloud_pi1
	 */
	protected function getTagCloudContent() {
		if ($content = $this->getCachedTagCloudContent()) {
			return $content;
		}
		return $this->setCachedTagCloudContent($this->getUncachedTagCloudContent());
	}

	/**
	 * Get tagCloud content from cache by indentifier
	 *
	 * @return mixed Content string on successfull get, FALSE on cache miss
	 */
	protected function getCachedTagCloudContent() {
		$cacheIdentifier = $this->getCacheIdentifier();
		return t3lib_div::makeInstance('tx_enetcache')->get($cacheIdentifier);
	}

	/**
	 * Instantiates and renders tag cloud content
	 *
	 * @return string Tag cloud content
	 */
	protected function getUncachedTagCloudContent() {
		$this->instantiateTagCloudObject();
		$this->initializeTagCloudObject();
		return $this->renderTagCloudContent();
	}

	/**
	 * Set tag cloud content to cache
	 *
	 * @param string HTML of tag cloud content
	 * @return string HTML of tag cloud content
	 */
	protected function setCachedTagCloudContent($content) {
		return t3lib_div::makeInstance('tx_enetcache')->set(
			$this->getCacheIdentifier(),
			$content,
			$this->getCacheTags(),
			$this->getCacheLifetime()
		);
	}

	/**
	 * Instantiate vge_tagcloud_pi1 object
	 *
	 * @return void
	 */
	protected function instantiateTagCloudObject() {
		require_once(t3lib_extMgm::extPath('vge_tagcloud', 'pi1/class.tx_vgetagcloud_pi1.php'));
		$this->tagCloudObject = t3lib_div::makeInstance('tx_vgetagcloud_pi1');
	}

	/**
	 * Initialize vge_tagcloud_pi1 object
	 *
	 * @return void
	 */
	protected function initializeTagCloudObject() {
		$this->tagCloudObject->cObj = &$this->cObj;
	}

	/**
	 * Render content of vge_tagcloud_pi1
	 *
	 * @return strin HTML content
	 */
	protected function renderTagCloudContent() {
		return $this->tagCloudObject->main('', $this->conf);
	}

	/**
	 * Get identifier from plugin configuration
	 *
	 * @return array Identifier parameters
	 */
	protected function getCacheIdentifier() {
		if ($this->cacheIdentifier) {
			return $this->cacheIdentifier;
		} else {
			return $this->initializeCacheIdentifier();
		}
	}

	/**
	 * Initialize cache identifier by given plugin configuration
	 *
	 * @return array Identifier parameters
	 */
	protected function initializeCacheIdentifier() {
		$this->cacheIdentifier = $this->conf;
		return $this->cacheIdentifier;
	}

	/**
	 * Cache tags of this cache entry
	 *
	 * @return array Cache tags
	 */
	protected function getCacheTags() {
		return array();
	}

	/**
	 * Return lifetime if given by TS, else default
	 *
	 * @return integer Lifetime
	 */
	protected function getCacheLifetime() {
		if ((int) $this->conf['cachetime'] > 0) {
			return (int) $this->conf['cachetime'];
		} else {
			return $this->defaultCacheLifetime;
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/classes/extensionwrappers/lass.tx_enetcache_extensionwrappers_vge_tagcloud_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/classes/extensionwrappers/lass.tx_enetcache_extensionwrappers_vge_tagcloud_pi1.php']);
}

?>
