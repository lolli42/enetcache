<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Add a new cache configuration if not already set in localconf.php
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache'] = [];
}
// Use StringFrontend if not set otherwise, if not set, core would choose variable frontend
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache']['frontend'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_enetcache_contentcache']['frontend'] = \TYPO3\CMS\Core\Cache\Frontend\StringFrontend::class;
}

// Define caches that have to be tagged and dropped
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['enetcache']['TAG_CACHES'] = [
    'cache_enetcache_contentcache',
    'cache_pages',
];

// Initialize array for hooks. Other extensions can register here
if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['enetcache']['hooks']['tx_enetcache'])) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['enetcache']['hooks']['tx_enetcache'] = [];
}

// Configure BE hooks
if (TYPO3_MODE == 'BE') {
    // Add the "Delete plugin cache" button and its functionality
    $GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['enetcache::clearContentCache'] = \Lolli\Enetcache\Hooks\BackendContentCacheMethods::class . '->clearContentCache';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['additionalBackendItems']['cacheActions'][] = \Lolli\Enetcache\Hooks\BackendToolbarClearContentCache::class;

    // Drop cache tag handling in DataHandler on changing / inserting / adding records
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['enetcache'] = \Lolli\Enetcache\Hooks\DataHandlerFlushByTag::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['enetcache'] = \Lolli\Enetcache\Hooks\DataHandlerFlushByTag::class;

    // Scheduler task to drop cache entries by tags
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Lolli\Enetcache\Tasks\DropTagsTask::class] = [
        'extension' => 'enetcache',
        'title' => 'LLL:EXT:enetcache/Resources/Private/Language/locallang.xlf:scheduler.droptags.name',
        'description' => 'LLL:EXT:enetcache/Resources/Private/Language/locallang.xlf:scheduler.droptags.description',
        'additionalFields' => \Lolli\Enetcache\Tasks\DropTagsAdditionalFieldProvider::class,
    ];

    // CLI script to drop cache entries by tags
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['cliKeys']['enetcache'] = [
        function () {
            $adminObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Lolli\Enetcache\Command\FlushCacheByTagCommand::class);
            $adminObj->cli_main();
        },
        '_CLI_enetcache'
    ];
}
