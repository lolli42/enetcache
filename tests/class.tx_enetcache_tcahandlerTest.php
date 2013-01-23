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
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Test cases for class tx_enetcache_tcahandler
 *
 * @author Michael Knabe <mk@e-netconsulting.de>
 */
class tx_enetcache_tcahandlerTest extends Tx_Phpunit_TestCase {

	/**
	 * @var t3lib_DB Backup of $GLOBALS['TYPO3_DB']
	 */
	protected $typo3DbBackup = NULL;

	/**
	 * Enable backup of global and system variables
	 *
	 * @var boolean
	 */
	protected $backupGlobals = TRUE;

	/**
	 * Exclude TYPO3_DB from backup/ restore of $GLOBALS
	 * because resource types cannot be handled during serializing.
	 * It is handled explicitly in setUp() and tearDown()
	 *
	 * @var array
	 */
	protected $backupGlobalsBlacklist = array('TYPO3_DB');

	/**
	 * Default set up mocks TYPO3_DB
	 */
	public function setUp() {
		$this->typo3DbBackup = $GLOBALS['TYPO3_DB'];
		$GLOBALS['TYPO3_DB'] = $this->getMock('t3lib_DB', array());
	}

	/**
	 * Default tear down restores TYPO3_DB
	 */
	public function tearDown() {
		$GLOBALS['TYPO3_DB'] = $this->typo3DbBackup;
	}

	/**
	 * @test
	 */
	public function findReferedDatabaseEntriesReturnsEmptyArrayForTcaWithoutRelations() {
		$GLOBALS['TCA']['testtable'] = require(t3lib_extMgm::extPath('enetcache', 'tests/fixtures/tca_without_references.php'));
		$this->assertEquals(
			array(),
			tx_enetcache_tcaHandler::findReferedDatabaseEntries('testtable', array(), 23)
		);
	}
	
	/**
	 * Data provider
	 */
	public function dataProviderForTcaWithRelations() {
		return array(
			'Table with relations' => array(
				require(t3lib_extMgm::extPath('enetcache', 'tests/fixtures/tca_with_references.php'))
			)
		);
	}
	
	/**
	 * @test
	 * @dataProvider dataProviderForTcaWithRelations
	 */
	public function findReferedDatabaseEntriesReturnsEmptyArrayForTcaWithRelationsAndNoExistingDatabaseEntry($tca) {
		$GLOBALS['TCA']['testtable'] = $tca;
		$this->assertEquals(
			array(),
			tx_enetcache_tcaHandler::findReferedDatabaseEntries('testtable', array(), 23)
		);
	}

	/**
	 * @test
	 * @dataProvider dataProviderForTcaWithRelations
	 */
	public function findReferedDatabaseEntriesReturnsEmptyArrayForTcaWithRelationsAndNoExistingDatabaseEntryWithReferenceToZeroGiven($tca) {
		$GLOBALS['TCA']['testtable'] = $tca;
		$this->assertEquals(
			array(),
			tx_enetcache_tcaHandler::findReferedDatabaseEntries('testtable', array('cust_stuff' =>'0'), 23)
		);
	}

	/**
	 * @test
	 * @dataProvider dataProviderForTcaWithRelations
	 */
	public function findReferedDatabaseEntriesReturnsRelationsForTcaWithRelationsAndDbWithNamedTables($tca) {
		$GLOBALS['TCA']['testtable'] = $tca;
		$this->assertEquals(
			array('foo_21', 'bar_42'),
			tx_enetcache_tcaHandler::findReferedDatabaseEntries('testtable', array('cust_stuff' => 'foo_21, bar_42'), 23)
		);
	}

	/**
	 * @test
	 * @dataProvider dataProviderForTcaWithRelations
	 */
	public function findReferedDatabaseEntriesReturnsRelationsForTcaWithRelationsAndDbWithNumericalReferences($tca) {
		$GLOBALS['TCA']['testtable'] = $tca;
		$this->assertEquals(
			array('fe_users_21', 'fe_users_42'),
			tx_enetcache_tcaHandler::findReferedDatabaseEntries('testtable', array('cust_fe_user' => '21, 42'), 23)
		);
	}

	/**
	 * Data provider
	 */
	public function dataProviderForTcaWithMmRelationsAndDb() {
		return array(
			'Table with relations' => array(
				require(t3lib_extMgm::extPath('enetcache', 'tests/fixtures/tca_with_mm_references.php'))
			)
		);
	}

	/**
	 * @test
	 * @dataProvider dataProviderForTcaWithMmRelationsAndDb
	 */
	public function findReferedDatabaseEntriesReturnsRelationsForTcaWithMmRelationsAndDb($tca) {
		$GLOBALS['TCA']['testtable'] = $tca;
		$GLOBALS['TYPO3_DB']->expects($this->once())
			->method('exec_SELECTgetRows')
			->with($this->equalTo('*'), $this->equalTo('tx_commerce_products_related_mm'), $this->equalTo('uid_local=23'))
			->will($this->returnValue(
				array(
					array('uid_local' => 23, 'uid_foreign' => 123),
					array('uid_local' => 23, 'uid_foreign' => 4711),
				)
			));
		$this->assertEquals(
			array('tx_commerce_products_123', 'tx_commerce_products_4711'),
			tx_enetcache_tcaHandler::findReferedDatabaseEntries('testtable', array(), 23)
		);
	}

	/**
	 * @test
	 * @dataProvider dataProviderForTcaWithMmRelationsAndDb
	 */
	public function findReferedDatabaseEntriesReturnsRelationsForTcaWithMmRelationsDbAndTablenames($tca) {
		$GLOBALS['TCA']['testtable'] = $tca;
		$GLOBALS['TYPO3_DB']->expects($this->once())
			->method('exec_SELECTgetRows')
			->with($this->equalTo('*'), $this->equalTo('tx_commerce_products_related_mm'), $this->equalTo('uid_local=23'))
			->will($this->returnValue(
				array(
					array('uid_local' => 23, 'uid_foreign' => 123, 'tablenames' => 'foo'),
					array('uid_local' => 23, 'uid_foreign' => 4711, 'tablenames' => 'bar'),
				)
			));
		$this->assertEquals(
			array('foo_123', 'bar_4711'),
			tx_enetcache_tcaHandler::findReferedDatabaseEntries('testtable', array(), 23)
		);
	}
}
?>