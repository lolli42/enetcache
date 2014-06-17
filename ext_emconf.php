<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "enetcache".
 *
 * Auto generated 04-07-2013 15:37
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Plugin cache engine',
	'description' => 'Provides an interface to cache plugin content elements based on 4.3 caching framework',
	'category' => 'Frontend',
	'shy' => 0,
	'version' => '1.1.1',
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
			'typo3' => '4.5.0-6.2.99',
			'php' => '5.3.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:32:{s:16:"ext_autoload.php";s:4:"2974";s:21:"ext_conf_template.txt";s:4:"9a0a";s:12:"ext_icon.gif";s:4:"daf4";s:17:"ext_localconf.php";s:4:"a2e6";s:14:"ext_tables.php";s:4:"ecb0";s:14:"ext_tables.sql";s:4:"1f02";s:13:"locallang.xml";s:4:"3522";s:17:"locallang_csh.xml";s:4:"5508";s:30:"Classes/class.tx_enetcache.php";s:4:"c0ac";s:35:"Classes/class.tx_enetcache_hook.php";s:4:"85e2";s:41:"Classes/class.tx_enetcache_tcahandler.php";s:4:"00c7";s:33:"Classes/Utility/Compatibility.php";s:4:"e63f";s:78:"Classes/extensionwrappers/class.tx_enetcache_extensionwrappers_pi_abstract.php";s:4:"d984";s:83:"Classes/extensionwrappers/class.tx_enetcache_extensionwrappers_vge_tagcloud_pi1.php";s:4:"8cc4";s:78:"Classes/extensionwrappers/class.tx_enetcache_extensionwrappers_wec_map_pi1.php";s:4:"3383";s:78:"Classes/extensionwrappers/class.tx_enetcache_extensionwrappers_wec_map_pi2.php";s:4:"fa3a";s:78:"Classes/extensionwrappers/class.tx_enetcache_extensionwrappers_wec_map_pi3.php";s:4:"2b7d";s:86:"Classes/extensionwrappers/class.tx_enetcache_extensionwrappers_wec_map_pi_abstract.php";s:4:"9a18";s:30:"cli/class.tx_enetcache_cli.php";s:4:"45b5";s:14:"doc/manual.sxw";s:4:"91d4";s:54:"hooks/class.tx_enetcache_backendContentCacheAction.php";s:4:"5581";s:55:"hooks/class.tx_enetcache_backendContentCacheMethods.php";s:4:"4923";s:36:"hooks/class.tx_enetcache_tcemain.php";s:4:"f019";s:46:"interfaces/interface.tx_enetcache_cachable.php";s:4:"fd70";s:46:"interfaces/interface.tx_enetcache_hookable.php";s:4:"d781";s:17:"res/delete_pi.png";s:4:"5092";s:42:"tasks/class.tx_enetcache_task_droptags.php";s:4:"0844";s:66:"tasks/class.tx_enetcache_task_droptags_additionalfieldprovider.php";s:4:"63d6";s:43:"tests/class.tx_enetcache_tcahandlerTest.php";s:4:"e573";s:41:"tests/fixtures/tca_with_mm_references.php";s:4:"8bde";s:38:"tests/fixtures/tca_with_references.php";s:4:"b637";s:41:"tests/fixtures/tca_without_references.php";s:4:"4016";}',
	'suggests' => array(
	),
);

?>