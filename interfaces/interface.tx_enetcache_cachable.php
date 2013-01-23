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
 * Defines the methods needed to make a controller cachable.
 *
 * @author Michael Knabe <mk@e-netconsulting.de>
 */
interface tx_enetcache_cachable {
	/**
	 * Returns an identifier that allows the plugin output to be identified w/o doubt.
	 * Usual parameters are piVars, some flexform / TS values, etc.
	 *
	 * @return array All variables relevant for exact matching of the cache entry
	 */
	public function getIdentifier();

	/**
	 * Returns an array with the tags that should be added when writing the cache entry
	 *
	 * @return array Tags of this cache entry
	 */
	public function getTags();
}

?>
