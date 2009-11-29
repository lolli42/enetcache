<?php

/**
 * @TODO: Major rework
 */


require_once t3lib_extMgm::extPath('enetcache').'classes/class.tx_enetcache.php';
require_once t3lib_extMgm::extPath('cms').'tslib/class.tslib_content.php';
define('TYPO3_MODE', 'blawurstgedingens');
require_once t3lib_extMgm::extPath('enetcache').'ext_localconf.php';
/**
 * tx_enetcache test case.
 */
class tx_enetcache_testcase extends tx_phpunit_testcase {
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		$GLOBALS['typo3CacheManager']->setCacheFactory(t3lib_div::makeInstance(t3lib_cache_Factory));
		$GLOBALS['typo3CacheManager']->setCacheConfigurations(array('cache_enetcache_content'=>$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_enetcache_content']));
		$GLOBALS['typo3CacheManager']->initialize();
	}

	/**
	 * Tests Subpart substitution
	 */
	public function testAddingCacheEntry() {
		tx_enetcache::set(array(1, $this, 'test', array('more', 'test'),1), 'cached data', array('more', 'test'));
		$this->assertEquals('cached data', tx_enetcache::get(array(1, $this, 'test', array('more', 'test'), 1)));
		tx_enetcache::drop(array('more', 'test'), 1);
	}
}
?>
