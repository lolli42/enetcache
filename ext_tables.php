<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

	// Add context sensitive help (csh) to the backend module (used for the scheduler tasks)
t3lib_extMgm::addLLrefForTCAdescr('_MOD_tools_txschedulerM1', 'EXT:' . $_EXTKEY . '/locallang_csh.xml');

	// Use sprite icon API for clearContentCache icon in BE cache topMenu, 4.4 and above
$enetcacheIcons = array(
	'clearcontentcache' => t3lib_extMgm::extRelPath('enetcache') . 'res/delete_pi.png',
);
t3lib_SpriteManager::addSingleIcons($enetcacheIcons, 'enetcache');
?>