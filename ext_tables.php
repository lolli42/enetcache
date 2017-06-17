<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Add context sensitive help (csh) to the backend module (used for the scheduler tasks)
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('_MOD_tools_txschedulerM1', 'EXT:enetcache/Resources/Private/Language/locallang_csh.xlf');

// Use sprite icon API for clearContentCache icon in BE cache top Menu
$enetcacheIcons = [
    'clearcontentcache' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('enetcache') . 'Resources/Public/Icons/FlushPluginCache.png',
];
\TYPO3\CMS\Backend\Sprite\SpriteManager::addSingleIcons($enetcacheIcons, 'enetcache');
