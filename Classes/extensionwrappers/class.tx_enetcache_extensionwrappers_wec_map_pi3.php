<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2013 Christian Kuhn <lolli@schwarzbu.ch>
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
 * enetcache wrapper class for pi3 classes of wec_map
 *
 * Enable transparently for a working wec_map_pi3 with TS:
 * plugin.tx_wecmap_pi3.userFunc = tx_enetcache_extensionwrappers_wec_map_pi3->main
 *
 * Set cache lifetime with TS:
 * plugin.tx_wecmap_pi3.cachetime = 1234
 *
 * @depends wec_map
 */
class tx_enetcache_extensionwrappers_wec_map_pi3 extends tx_enetcache_extensionwrappers_wec_map_pi_abstract {
	/**
	 * Instantiate wec_map_pi3 object
	 *
	 * @return tx_wecmap_pi3 Instance of wec_map_pi3
	 */
	protected function instantiateObject() {
			// Require is needed as long as wec_map does not come with an ext_autoload.php file of this class
		require_once(t3lib_extMgm::extPath('wec_map', 'pi3/class.tx_wecmap_pi3.php'));
		return t3lib_div::makeInstance('tx_wecmap_pi3');
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/classes/extensionwrappers/class.tx_enetcache_extensionwrappers_wec_map_pi3.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/classes/extensionwrappers/class.tx_enetcache_extensionwrappers_wec_map_pi3.php']);
}

?>
