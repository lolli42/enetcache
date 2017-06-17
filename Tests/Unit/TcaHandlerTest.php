<?php
namespace Lolli\Enetcache\Tests\Unit;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Lolli\Enetcache\TcaHandler;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Test case
 */
class TcaHandlerTest extends UnitTestCase
{

    /**
     * Default set up mocks TYPO3_DB
     */
    public function setUp()
    {
        $GLOBALS['TYPO3_DB'] = $this->getMock(
            't3lib_DB',
            ['exec_SELECTquery', 'quoteStr', 'sql_fetch_assoc', 'exec_SELECTgetRows']
        );
    }

    /**
     * @test
     */
    public function findReferedDatabaseEntriesReturnsEmptyArrayForTcaWithoutRelations()
    {
        $GLOBALS['TCA']['testtable'] = require(__DIR__ . '/Fixtures/tca_without_references.php');
        $this->assertEquals(
            [],
            TcaHandler::findReferedDatabaseEntries('testtable', [], 23)
        );
    }

    /**
     * @test
     */
    public function findReferedDatabaseEntriesReturnsEmptyArrayForTcaWithRelationsAndNoExistingDatabaseEntry()
    {
        $GLOBALS['TCA']['testtable'] = require(__DIR__ . '/Fixtures/tca_with_references.php');
        $this->assertEquals(
            [],
            TcaHandler::findReferedDatabaseEntries('testtable', [], 23)
        );
    }

    /**
     * @test
     */
    public function findReferedDatabaseEntriesReturnsEmptyArrayForTcaWithRelationsAndNoExistingDatabaseEntryWithReferenceToZeroGiven()
    {
        $GLOBALS['TCA']['testtable'] = require(__DIR__ . '/Fixtures/tca_with_references.php');
        $this->assertEquals(
            [],
            TcaHandler::findReferedDatabaseEntries('testtable', ['cust_stuff' =>'0'], 23)
        );
    }

    /**
     * @test
     */
    public function findReferedDatabaseEntriesReturnsRelationsForTcaWithRelationsAndDbWithNamedTables()
    {
        $GLOBALS['TCA']['testtable'] = require(__DIR__ . '/Fixtures/tca_with_references.php');
        $this->assertEquals(
            ['foo_21', 'bar_42'],
            TcaHandler::findReferedDatabaseEntries('testtable', ['cust_stuff' => 'foo_21, bar_42'], 23)
        );
    }

    /**
     * @test
     */
    public function findReferedDatabaseEntriesReturnsRelationsForTcaWithRelationsAndDbWithNumericalReferences()
    {
        $GLOBALS['TCA']['testtable'] = require(__DIR__ . '/Fixtures/tca_with_references.php');
        $this->assertEquals(
            ['fe_users_21', 'fe_users_42'],
            TcaHandler::findReferedDatabaseEntries('testtable', ['cust_fe_user' => '21, 42'], 23)
        );
    }

    /**
     * @test
     */
    public function findReferedDatabaseEntriesReturnsRelationsForTcaWithMmRelationsAndDb()
    {
        $GLOBALS['TCA']['testtable'] = require(__DIR__ . '/Fixtures/tca_with_mm_references.php');
        $GLOBALS['TYPO3_DB']->expects($this->once())
            ->method('exec_SELECTgetRows')
            ->with($this->equalTo('*'), $this->equalTo('tx_commerce_products_related_mm'), $this->equalTo('uid_local=23'))
            ->will($this->returnValue(
                [
                    ['uid_local' => 23, 'uid_foreign' => 123],
                    ['uid_local' => 23, 'uid_foreign' => 4711],
                ]
            ));
        $this->assertEquals(
            ['tx_commerce_products_123', 'tx_commerce_products_4711'],
            TcaHandler::findReferedDatabaseEntries('testtable', [], 23)
        );
    }

    /**
     * @test
     */
    public function findReferedDatabaseEntriesReturnsRelationsForTcaWithMmRelationsDbAndTablenames()
    {
        $GLOBALS['TCA']['testtable'] = require(__DIR__ . '/Fixtures/tca_with_mm_references.php');
        $GLOBALS['TYPO3_DB']->expects($this->once())
            ->method('exec_SELECTgetRows')
            ->with($this->equalTo('*'), $this->equalTo('tx_commerce_products_related_mm'), $this->equalTo('uid_local=23'))
            ->will($this->returnValue(
                [
                    ['uid_local' => 23, 'uid_foreign' => 123, 'tablenames' => 'foo'],
                    ['uid_local' => 23, 'uid_foreign' => 4711, 'tablenames' => 'bar'],
                ]
            ));
        $this->assertEquals(
            ['foo_123', 'bar_4711'],
            TcaHandler::findReferedDatabaseEntries('testtable', [], 23)
        );
    }
}
