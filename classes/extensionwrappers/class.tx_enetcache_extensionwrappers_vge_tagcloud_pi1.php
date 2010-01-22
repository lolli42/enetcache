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
 * enetcache implementation as stupid wrapper for vge_tagcloud
 *
 * Enable transparently for a working vge_tagcloud_pi1 with TS:
 * plugin.tx_vgetagcloud_pi1.userFunc = tx_enetcache_extensionwrappers_vge_tagcloud_pi1->main
 *
 * Set cache lifetime with TS:
 * plugin.tx_vgetagcloud_pi1.cachetime = 1234
 *
 * Feel free to extend this with an own class to add better tagging or other fancy stuff
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
	 * @var integer Default lifetime 1 hour if not set
	 */
	protected $defaultCacheLifetime = 3600;

	/**
	 * Main method. It returns HTML content of vge_tagcloud_pi1 plugin
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
		$this->pi_initPIflexForm();
	}

	/**
	 * Get content of vge_tagcloud from cache, or render content and set to cache
	 *
	 * @return string HTML Content of vge_tagcloud_pi1
	 */
	protected function getTagCloudContent() {
		if ($content = $this->getCachedTagCloudContent()) {
			return $content;
		}
		return $this->setTagCloudContentCache($this->getNewTagCloudContent());
	}

	/**
	 * Get tagCloud content from cache by indentifier
	 *
	 * @return mixed Content string on successfull get, FALSE on cache miss
	 */
	protected function getCachedTagCloudContent() {
		return t3lib_div::makeInstance('tx_enetcache')->get($this->getCacheIdentifier());
	}

	/**
	 * Instantiates and renders tag cloud content
	 *
	 * @return string Tag cloud content
	 */
	protected function getNewTagCloudContent() {
		$tagCloudObject = $this->instantiateTagCloudObject();
		$tagCloudObject = $this->initializeTagCloudObject($tagCloudObject);
		return $this->renderTagCloudContent($tagCloudObject);
	}

	/**
	 * Set tag cloud content to cache
	 *
	 * @param string HTML of tag cloud content
	 * @return string HTML of tag cloud content
	 */
	protected function setTagCloudContentCache($content) {
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
	 * @return tx_vgetagcloud_pi1 Instance of tx_vgetagcloud_pi1
	 */
	protected function instantiateTagCloudObject() {
			// Require is needed as long as vge_tagcloud does not come with an ext_autoload.php file of this class
		require_once(t3lib_extMgm::extPath('vge_tagcloud', 'pi1/class.tx_vgetagcloud_pi1.php'));
		return t3lib_div::makeInstance('tx_vgetagcloud_pi1');
	}

	/**
	 * Initialize vge_tagcloud_pi1 object
	 *
	 * @param tx_vgetagcloud_pi1 Instance of tx_vgetagcloud_pi1
	 * @return tx_vgetagcloud_pi1 Initialized tx_vgetagcloud_pi1
	 */
	protected function initializeTagCloudObject(tx_vgetagcloud_pi1 $tagCloudObject) {
		$tagCloudObject->cObj = &$this->cObj;
		return $tagCloudObject;
	}

	/**
	 * Render content of vge_tagcloud_pi1
	 *
	 * @param tx_vgetagcloud_pi1 Fully initialized tagcloud object
	 * @return string HTML content
	 */
	protected function renderTagCloudContent(tx_vgetagcloud_pi1 $tagCloudObject) {
		return $tagCloudObject->main('', $this->conf);
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
	 * plugin.tx_vgetagcloud_pi1.cachetime = 1234
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

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/classes/extensionwrappers/lass.tx_enetcache_extensionwrappers_vge_tagcloud_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/classes/extensionwrappers/lass.tx_enetcache_extensionwrappers_vge_tagcloud_pi1.php']);
}

?>
