<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2013 Michael Knabe <mk@e-netconsulting.de>
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
 * tx_enetcache_hook
 *
 * An abstract class with NULL implementations of all interface methods.
 * Extend this class with your own hook class.
 *
 * @author  Michael Knabe <mk@e-netconsulting.de>
 * @author  Christian Kuhn <lolli@schwarzbu.ch>
 * @see interface tx_enetcache_hookable
 */
abstract class tx_enetcache_hook implements tx_enetcache_hookable {

	public function get($params) {}

	public function set(&$params) {}

	public function flush() {}

	public function drop(&$params) {}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/class.tx_enetcache_hook.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/class.tx_enetcache_hook.php']);
}

?>
