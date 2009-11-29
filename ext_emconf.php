<?php

########################################################################
# Extension Manager/Repository config file for ext: "enetcache"
#
# Auto generated 19-08-2009 16:32
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Plugin cache engine',
	'description' => 'Provides an interface to cache plugin content elements based on 4.3 caching framework',
	'category' => 'Frontend',
	'author' => 'Michael Knabe',
	'author_email' => 'mk@e-netconsulting.de',
	'shy' => '',
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => 'e-netconsulting KG',
	'version' => '0.8.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.3.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'enetcacheanalytics' => '0.0.0',
			'scheduler' => '0.0.0',
		),
	),
	'_md5_values_when_last_written' => 'a:5:{s:9:"ChangeLog";s:4:"ae6d";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:19:"doc/wizard_form.dat";s:4:"a811";s:20:"doc/wizard_form.html";s:4:"b0f4";}',
);

?>
