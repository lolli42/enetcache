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
//		$GLOBALS['typo3CacheManager']->setCacheFactory(t3lib_div::makeInstance('t3lib_cache_Factory'));
//		$GLOBALS['typo3CacheManager']->setCacheConfigurations(array('cache_enetcache_content'=>$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache']));
		//		$GLOBALS['typo3CacheManager']->initialize();
		$this->cache = t3lib_div::makeInstance('tx_enetcache');
		$this->tags = array();
		for($i = 0; $i<5000; $i++) {
			$this->tags[] = strval(rand());
		}
	}

	/**
	 * Tests Subpart substitution
	 */
	public function testAddingCacheEntry() {
		$this->cache->set(array(1, $this, 'test', array('more', 'test'),1), 'cached data', array('more', 'test'));
		
		$this->assertEquals('cached data', $this->cache->get(array(1, $this, 'test', array('more', 'test'), 1)));

			//Cleanup
		$this->cache->drop(array('more', 'test'));
	}

	public function testSetManyTags() {
		$microtimeBeforeSet = microtime(1);
		$this->cache->set(array('cachetest'), 'test data', $this->tags);
		$microtimeAfterSet = microtime(1);
		$this->assertLessThan(0.0001, $microtimeAfterSet - $microtimeBeforeSet, 'Setting takes too long');
	}

	public function testDropManyTags() {
		$microtimeBeforeDrop = microtime(1);
		$this->cache->drop($this->tags);
		$microtimeAfterDrop = microtime(1);
		$this->assertLessThan(0.0001, $microtimeAfterDrop - $microtimeBeforeDrop, 'Dropping takes too long');

	}
}
?>
