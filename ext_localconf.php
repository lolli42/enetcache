<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

	// Register own implementation of a compressed db backend for TYPO3 versions 4.3 and 4.4.
	// @obsolete since TYPO3 4.5: Use compress option of core database backend instead
if (t3lib_div::int_from_ver(TYPO3_version) <= '4004999') {
	$TYPO3_CONF_VARS['SYS']['caching']['cacheBackends']['tx_enetcache_cache_backend_CompressedDbBackend']
		= 'typo3conf/ext/enetcache/classes/class.tx_enetcache_cache_backend_compresseddbbackend.php:tx_enetcache_cache_backend_CompressedDbBackend';
}

	// Add a new cache configuration if not already set in localconf.php
if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache'])) {
	$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache'] = array(
		'frontend' => 't3lib_cache_frontend_StringFrontend',
		'backend' => 't3lib_cache_backend_DbBackend',
		'options' => array(
			'cacheTable' => 'tx_enetcache_contentcache',
			'tagsTable' => 'tx_enetcache_contentcache_tags',
		),
	);
}

	// Define caches that have to be tagged and dropped
$TYPO3_CONF_VARS['EXTCONF']['enetcache']['TAG_CACHES'] = array(
	'cache_enetcache_contentcache',
	'cache_pages',
);

	// Initialize array for hooks. Other extensions can register here (like enetcacheanalytics)
if (!isset($TYPO3_CONF_VARS['EXTCONF']['enetcache']['hooks']['tx_enetcache'])) {
	$TYPO3_CONF_VARS['EXTCONF']['enetcache']['hooks']['tx_enetcache'] = array();
}

	// Configure BE hooks
if (TYPO3_MODE == 'BE') {
		// Add the "Delete plugin cache" button and its functionality
	$TYPO3_CONF_VARS['BE']['AJAX']['enetcache::clearContentCache'] =
		'EXT:enetcache/hooks/class.tx_enetcache_backendContentCacheMethods.php:tx_enetcache_backendContentCacheMethods->clearContentCache';
	$TYPO3_CONF_VARS['SC_OPTIONS']['additionalBackendItems']['cacheActions'][] =
		'EXT:enetcache/hooks/class.tx_enetcache_backendContentCacheAction.php:tx_enetcache_backendContentCacheAction';

		// Clear our cache table on "Clear all cache" click and "TCEMAIN.clearCacheCmd = all"
	$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] =
		'EXT:enetcache/hooks/class.tx_enetcache_backendContentCacheMethods.php:tx_enetcache_backendContentCacheMethods->clearCachePostProc';

		// Drop cache tag handling in tcemain on changing / inserting / adding records
	$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['enetcache'] =
		'EXT:enetcache/hooks/class.tx_enetcache_tcemain.php:tx_enetcache_tcemain';
	$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['enetcache'] =
		'EXT:enetcache/hooks/class.tx_enetcache_tcemain.php:tx_enetcache_tcemain';

		// Scheduler task for garbage collection of cache backends (especially db- and fileBackend)
		// @obsolete since TYPO3 4.5, use the core task instead
	if (t3lib_div::int_from_ver(TYPO3_version) <= '4004999') {
		$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks']['tx_enetcache_gccachebackends'] = array(
			'extension' => $_EXTKEY,
			'title' => 'LLL:EXT:enetcache/locallang.xml:scheduler.gccachebackends.name',
			'description' => 'LLL:EXT:enetcache/locallang.xml:scheduler.gccachebackends.description',
			'additionalFields' => 'tx_enetcache_gccachebackends_additionalfieldprovider',
		);
	}

		// Scheduler task to drop cache entries by tags
	$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks']['tx_enetcache_task_DropTags'] = array(
		'extension' => $_EXTKEY,
		'title' => 'LLL:EXT:enetcache/locallang.xml:scheduler.droptags.name',
		'description' => 'LLL:EXT:enetcache/locallang.xml:scheduler.droptags.description',
		'additionalFields' => 'tx_enetcache_task_droptags_additionalfieldprovider',
	);

		// CLI script to drop cache entries by tags
	$TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['cliKeys'][$_EXTKEY] = array(
		'EXT:enetcache/cli/class.tx_enetcache_cli.php', '_CLI_enetcache'
	);
}
?>
