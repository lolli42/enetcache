<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2011 Christian Kuhn <lolli@schwarzbu.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * enetcache implementation as stupid wrapper for vge_tagcloud
 *
 * Enable transparently for a working vge_tagcloud_pi1 with TS:
 * plugin.tx_vgetagcloud_pi1.userFunc = tx_enetcache_extensionwrappers_vge_tagcloud_pi1->main
 *
 * Set cache lifetime with TS:
 * plugin.tx_vgetagcloud_pi1.cachetime = 1234
 *
 * Feel free to extend this with an own class to add better tagging or other fancy stuff
 *
 * @package TYPO3
 * @subpackage enetcache
 * @depends vge_tagcloud
 */
class tx_enetcache_extensionwrappers_vge_tagcloud_pi1 extends tx_enetcache_extensionwrappers_pi_abstract {
	/**
	 * Instantiate vge_tagcloud_pi1 object
	 *
	 * @return tx_vgetagcloud_pi1 Instance of tx_vgetagcloud_pi1
	 */
	protected function instantiateObject() {
			// Require is needed as long as vge_tagcloud does not come with an ext_autoload.php file of this class
		require_once(t3lib_extMgm::extPath('vge_tagcloud', 'pi1/class.tx_vgetagcloud_pi1.php'));
		return t3lib_div::makeInstance('tx_vgetagcloud_pi1');
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/classes/extensionwrappers/class.tx_enetcache_extensionwrappers_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/classes/extensionwrappers/class.tx_enetcache_extensionwrappers_pi1.php']);
}

?>
