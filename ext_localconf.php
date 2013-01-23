<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

	// Add a new cache configuration if not already set in localconf.php
if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache'])) {
	$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache'] = array();
}
	// Use StringFrontend if not set otherwise, if not set, core would choose variable frontend
if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache']['frontend'])) {
	$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache']['frontend'] = 't3lib_cache_frontend_StringFrontend';
}
	// Add cache settings for core versions below 4.6
if (Tx_Enetcache_Utility_Compatibility::convertVersionNumberToInteger(TYPO3_version) <= '4005999') {
	if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache']['backend'])) {
		$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache']['backend'] = 't3lib_cache_backend_DbBackend';
	}
	if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache']['options'])) {
		$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache']['options'] = array();
	}
	if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache']['options']['cacheTable'])) {
		$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache']['options']['cacheTable'] = 'tx_enetcache_contentcache';
	}
	if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache']['options']['tagsTable'])) {
		$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache']['options']['tagsTable'] = 'tx_enetcache_contentcache_tags';
	}
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
		// Done by core automatically since 4.6
	if (Tx_Enetcache_Utility_Compatibility::convertVersionNumberToInteger(TYPO3_version) <= '4005999') {
		$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] =
			'EXT:enetcache/hooks/class.tx_enetcache_backendContentCacheMethods.php:tx_enetcache_backendContentCacheMethods->clearCachePostProc';
	}

		// Drop cache tag handling in tcemain on changing / inserting / adding records
	$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['enetcache'] =
		'EXT:enetcache/hooks/class.tx_enetcache_tcemain.php:tx_enetcache_tcemain';
	$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['enetcache'] =
		'EXT:enetcache/hooks/class.tx_enetcache_tcemain.php:tx_enetcache_tcemain';

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