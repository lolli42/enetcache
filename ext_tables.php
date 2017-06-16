<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

// Add context sensitive help (csh) to the backend module (used for the scheduler tasks)
t3lib_extMgm::addLLrefForTCAdescr('_MOD_tools_txschedulerM1', 'EXT:enetcache/locallang_csh.xml');

// Use sprite icon API for clearContentCache icon in BE cache top Menu
$enetcacheIcons = [
	'clearcontentcache' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('enetcache') . 'Resources/Public/Icons/FlushPluginCache.png',
];
t3lib_SpriteManager::addSingleIcons($enetcacheIcons, 'enetcache');