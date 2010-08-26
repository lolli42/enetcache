<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

	// Add context sensitive help (csh) to the backend module (used for the scheduler tasks)
t3lib_extMgm::addLLrefForTCAdescr('_MOD_tools_txschedulerM1', 'EXT:' . $_EXTKEY . '/locallang_csh.xml');
?>
