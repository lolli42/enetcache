<?php

########################################################################
# Extension Manager/Repository config file for ext "enetcache".
#
# Auto generated 07-02-2010 19:25
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Plugin cache engine',
	'description' => 'Provides an interface to cache plugin content elements based on 4.3 caching framework',
	'category' => 'Frontend',
	'shy' => 0,
	'version' => '0.9.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Michael Knabe',
	'author_email' => 'mk@e-netconsulting.de',
	'author_company' => 'e-netconsulting KG',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.3.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:28:{s:16:"ext_autoload.php";s:4:"03d6";s:21:"ext_conf_template.txt";s:4:"9a0a";s:12:"ext_icon.gif";s:4:"daf4";s:17:"ext_localconf.php";s:4:"c1a0";s:14:"ext_tables.sql";s:4:"947c";s:13:"locallang.xml";s:4:"a4c1";s:30:"classes/class.tx_enetcache.php";s:4:"d43e";s:64:"classes/class.tx_enetcache_cache_backend_compresseddbbackend.php";s:4:"d689";s:35:"classes/class.tx_enetcache_hook.php";s:4:"b7d7";s:41:"classes/class.tx_enetcache_tcahandler.php";s:4:"fae8";s:78:"classes/extensionwrappers/class.tx_enetcache_extensionwrappers_pi_abstract.php";s:4:"6718";s:83:"classes/extensionwrappers/class.tx_enetcache_extensionwrappers_vge_tagcloud_pi1.php";s:4:"1d82";s:78:"classes/extensionwrappers/class.tx_enetcache_extensionwrappers_wec_map_pi1.php";s:4:"17c6";s:78:"classes/extensionwrappers/class.tx_enetcache_extensionwrappers_wec_map_pi2.php";s:4:"1b4c";s:78:"classes/extensionwrappers/class.tx_enetcache_extensionwrappers_wec_map_pi3.php";s:4:"31a9";s:86:"classes/extensionwrappers/class.tx_enetcache_extensionwrappers_wec_map_pi_abstract.php";s:4:"df81";s:14:"doc/manual.sxw";s:4:"287d";s:54:"hooks/class.tx_enetcache_backendContentCacheAction.php";s:4:"2d16";s:36:"hooks/class.tx_enetcache_tcemain.php";s:4:"0031";s:46:"interfaces/interface.tx_enetcache_cachable.php";s:4:"9d9b";s:46:"interfaces/interface.tx_enetcache_hookable.php";s:4:"89d7";s:33:"patches/12858_01_typo3_4_3_0.diff";s:4:"3079";s:33:"patches/12859_01_typo3_4_3_0.diff";s:4:"8a3f";s:33:"patches/13273_01_typo3_4_3_0.diff";s:4:"0ac8";s:17:"res/delete_pi.png";s:4:"5092";s:44:"tasks/class.tx_enetcache_gccachebackends.php";s:4:"ccd6";s:68:"tasks/class.tx_enetcache_gccachebackends_additionalfieldprovider.php";s:4:"117c";s:37:"tests/class.tx_enetcache_testcase.php";s:4:"2c39";}',
);

?>