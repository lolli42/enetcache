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
 * A caching backend which stores compressed cache entries in database tables
 *
 * @depends: If using TYPO3 4.3, core patch #12858 from the patches directory must be applied
 * @obsolete This backend was merged with the default core database backend since TYPO3 4.5 with the 'compress' option
 * 		This backend can only be used with 4.4 and 4.3
 *
 * @package TYPO3
 * @subpackage enetcache
 */
class tx_enetcache_cache_backend_CompressedDbBackend extends t3lib_cache_backend_DbBackend {

	/**
	 * Constructs this backend
	 *
	 * @param mixed Configuration options - depends on the actual backend
	 */
	public function __construct(array $options = array()) {
		parent::__construct($options);
	}

	/**
	 * Saves data in a cache file.
	 *
	 * @param string An identifier for this specific cache entry
	 * @param string The data to be stored
	 * @param array Tags to associate with this cache entry
	 * @param integer Lifetime of this cache entry in seconds. If NULL is specified, the default lifetime is used. "0" means unlimited liftime.
	 * @return void
	 * @throws t3lib_cache_Exception if no cache frontend has been set.
	 * @throws t3lib_cache_exception_InvalidData if the data to be stored is not a string.
	 * @author Ingo Renner <ingo@typo3.org>
	 * @author Christian Kuhn <lolli@schwarzbu.ch>
	 */
	public function set($entryIdentifier, $data, array $tags = array(), $lifetime = NULL) {
		if (!$this->cache instanceof t3lib_cache_frontend_Frontend) {
			throw new t3lib_cache_Exception(
				'No cache frontend has been set via setCache() yet.',
				1236518288
			);
		}

		if (!is_string($data)) {
			throw new t3lib_cache_exception_InvalidData(
				'The specified data is of type "' . gettype($data) . '" but a string is expected.',
				1236518298
			);
		}

		if (is_null($lifetime)) {
			$lifetime = $this->defaultLifetime;
		}

		$this->remove($entryIdentifier);

		$data = $this->compressData($data, TRUE);

		$GLOBALS['TYPO3_DB']->exec_INSERTquery(
			$this->cacheTable,
			array(
				'identifier' => $entryIdentifier,
				'crdate'     => $GLOBALS['EXEC_TIME'],
				'content'    => $data,
				'lifetime'   => $lifetime
			)
		);

		if (count($tags)) {
			$fields = array();
			$fields[] = 'identifier';
			$fields[] = 'tag';

			$tagRows = array();
			foreach ($tags as $tag) {
				$tagRow = array();
				$tagRow[] = $entryIdentifier;
				$tagRow[] = $tag;
				$tagRows[] = $tagRow;
			}

			$GLOBALS['TYPO3_DB']->exec_INSERTmultipleRows(
				$this->tagsTable,
				$fields,
				$tagRows
			);
		}
	}

	/**
	 * Loads data from a cache file.
	 *
	 * @param string An identifier which describes the cache entry to load
	 * @return mixed The cache entry's data as a string or FALSE if the cache entry could not be loaded
	 * @author Ingo Renner <ingo@typo3.org>
	 * @author Christian Kuhn <lolli@schwarzbu.ch>
	 */
	public function get($entryIdentifier) {
		$cacheEntry = false;

		$cacheEntries = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'content',
			$this->cacheTable,
			'identifier = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($entryIdentifier, $this->cacheTable) . ' '
				. 'AND (crdate + lifetime >= ' . $GLOBALS['EXEC_TIME'] . ' OR lifetime = 0)'
		);

		if (count($cacheEntries) == 1) {
			$cacheEntry = $this->uncompressData($cacheEntries[0]['content'], FALSE);
		}

		return $cacheEntry;
	}

	/**
	 * Compress data with php's built in zlib
	 *
	 * @param string	Data to handle
	 * @return string	handled data
	 */
	protected function compressData($data = '') {
		if (strlen($data)) {
			$data = gzcompress($data);
		}
		return $data;
	}

	/**
	 * Uncompress data with php's built in zlib
	 *
	 * @param string	Data to handle
	 * @return string	handled data
	 */
	protected function uncompressData($data = '') {
		if (strlen($data)) {
			$data = gzuncompress($data);
		}
		return $data;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/class.tx_enetcache_cache_backend_compresseddbbackend.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/enetcache/class.tx_enetcache_cache_backend_compresseddbbackend.php']);
}

?>
