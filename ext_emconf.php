<?php

########################################################################
# Extension Manager/Repository config file for ext "enetcache".
#
# Auto generated 13-02-2012 21:45
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
	'version' => '1.0.6',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
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
	'_md5_values_when_last_written' => 'a:36:{s:16:"ext_autoload.php";s:4:"6461";s:21:"ext_conf_template.txt";s:4:"9a0a";s:12:"ext_icon.gif";s:4:"daf4";s:17:"ext_localconf.php";s:4:"1bdd";s:14:"ext_tables.php";s:4:"7074";s:14:"ext_tables.sql";s:4:"2ad0";s:13:"locallang.xml";s:4:"7681";s:17:"locallang_csh.xml";s:4:"9fae";s:30:"classes/class.tx_enetcache.php";s:4:"10b9";s:64:"classes/class.tx_enetcache_cache_backend_compresseddbbackend.php";s:4:"1d12";s:35:"classes/class.tx_enetcache_hook.php";s:4:"7384";s:41:"classes/class.tx_enetcache_tcahandler.php";s:4:"d55c";s:78:"classes/extensionwrappers/class.tx_enetcache_extensionwrappers_pi_abstract.php";s:4:"b817";s:83:"classes/extensionwrappers/class.tx_enetcache_extensionwrappers_vge_tagcloud_pi1.php";s:4:"71d4";s:78:"classes/extensionwrappers/class.tx_enetcache_extensionwrappers_wec_map_pi1.php";s:4:"5f51";s:78:"classes/extensionwrappers/class.tx_enetcache_extensionwrappers_wec_map_pi2.php";s:4:"ec6a";s:78:"classes/extensionwrappers/class.tx_enetcache_extensionwrappers_wec_map_pi3.php";s:4:"12de";s:86:"classes/extensionwrappers/class.tx_enetcache_extensionwrappers_wec_map_pi_abstract.php";s:4:"e831";s:30:"cli/class.tx_enetcache_cli.php";s:4:"5283";s:14:"doc/manual.sxw";s:4:"2312";s:54:"hooks/class.tx_enetcache_backendContentCacheAction.php";s:4:"d980";s:55:"hooks/class.tx_enetcache_backendContentCacheMethods.php";s:4:"d5be";s:36:"hooks/class.tx_enetcache_tcemain.php";s:4:"cca1";s:46:"interfaces/interface.tx_enetcache_cachable.php";s:4:"119c";s:46:"interfaces/interface.tx_enetcache_hookable.php";s:4:"f527";s:34:"patches/12858_02_typo3_4_3_10.diff";s:4:"32f0";s:34:"patches/12859_02_typo3_4_3_10.diff";s:4:"1527";s:17:"res/delete_pi.png";s:4:"5092";s:44:"tasks/class.tx_enetcache_gccachebackends.php";s:4:"f467";s:68:"tasks/class.tx_enetcache_gccachebackends_additionalfieldprovider.php";s:4:"1d33";s:42:"tasks/class.tx_enetcache_task_droptags.php";s:4:"4924";s:66:"tasks/class.tx_enetcache_task_droptags_additionalfieldprovider.php";s:4:"9987";s:43:"tests/class.tx_enetcache_tcahandlerTest.php";s:4:"18b1";s:41:"tests/fixtures/tca_with_mm_references.php";s:4:"8bde";s:38:"tests/fixtures/tca_with_references.php";s:4:"b637";s:41:"tests/fixtures/tca_without_references.php";s:4:"4016";}',
	'suggests' => array(
	),
);

?>