<?php
defined('TYPO3') or die();

// Add a new cache configuration if not already set
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['enetcachecontent'] ?? false)) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['enetcachecontent'] = [];
}
// Add plugin cache to 'pages' and 'all' group if not set otherwise yet
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['enetcachecontent']['groups'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['enetcachecontent']['groups'] = [
        'pages',
        'all'
    ];
}

// Define caches that have to be tagged and dropped
if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['enetcache']['TAG_CACHES'])) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['enetcache']['TAG_CACHES'] = [
        'enetcachecontent',
        'pages',
    ];
}

// Initialize array for hooks. Other extensions can register here
if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['enetcache']['hooks']['tx_enetcache'])) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['enetcache']['hooks']['tx_enetcache'] = [];
}

// Drop cache tag handling in DataHandler on changing / inserting / adding records
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['enetcache'] = \Lolli\Enetcache\Hooks\DataHandlerFlushByTagHook::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['enetcache'] = \Lolli\Enetcache\Hooks\DataHandlerFlushByTagHook::class;

// Scheduler task to drop cache entries by tags
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Lolli\Enetcache\Tasks\DropTagsTask::class] = [
    'extension' => 'enetcache',
    'title' => 'LLL:EXT:enetcache/Resources/Private/Language/locallang.xlf:scheduler.droptags.name',
    'description' => 'LLL:EXT:enetcache/Resources/Private/Language/locallang.xlf:scheduler.droptags.description',
    'additionalFields' => \Lolli\Enetcache\Tasks\DropTagsAdditionalFieldProvider::class,
];
