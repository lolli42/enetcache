<?php
namespace Lolli\Enetcache\Tests\Unit\Hooks;

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

use Lolli\Enetcache\Hooks\DataHandlerFlushByTagHook;
use Prophecy\Argument;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionContainerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 *
 * @todo: These tests shoud be migrated to functional tests.
 */
class DataHandlerFlushByTagHookTest extends UnitTestCase
{

    /**
     * @test
     */
    public function findReferencedDatabaseEntriesReturnsEmptyArrayForTcaWithoutRelations()
    {
        $connectionPoolProphecy = $this->prophesize(ConnectionPool::class);
        GeneralUtility::addInstance(ConnectionPool::class, $connectionPoolProphecy->reveal());
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $connectionPoolProphecy->getQueryBuilderForTable(Argument::cetera())->willReturn($queryBuilderProphecy->reveal());
        $restrictionContainerProphecy = $this->prophesize(QueryRestrictionContainerInterface::class);
        $queryBuilderProphecy->getRestrictions()->willReturn($restrictionContainerProphecy->reveal());
        $restrictionContainerProphecy->removeAll()->shouldBeCalled();
        $queryBuilderProphecy->select('*')->shouldBeCalled()->willReturn($queryBuilderProphecy->reveal());
        $queryBuilderProphecy->from('testtable')->shouldBeCalled()->willReturn($queryBuilderProphecy->reveal());
        $expressionBuilderProphecy = $this->prophesize(ExpressionBuilder::class);
        $queryBuilderProphecy->expr()->willReturn($expressionBuilderProphecy->reveal());
        $queryBuilderProphecy->createNamedParameter(Argument::cetera())->willReturnArgument(0);
        $expressionBuilderProphecy->eq(Argument::cetera())->willReturn('');
        $queryBuilderProphecy->where('')->shouldBeCalled()->willReturn($queryBuilderProphecy->reveal());
        $statementProphecy = $this->prophesize(\Doctrine\DBAL\Statement::class);
        $queryBuilderProphecy->execute()->shouldBeCalled()->willReturn($statementProphecy->reveal());
        $statementProphecy->fetch()->shouldBeCalled()->willReturn([]);

        $subject = new DataHandlerFlushByTagHook();
        $GLOBALS['TCA']['testtable'] = require(__DIR__ . '/Fixtures/tca_without_references.php');
        $this->assertEquals(
            [],
            $this->callInaccessibleMethod($subject, 'findReferencedDatabaseEntries', 'testtable', [], 23)
        );
    }

    /**
     * @test
     */
    public function findReferencedDatabaseEntriesReturnsEmptyArrayForTcaWithRelationsAndNoExistingDatabaseEntry()
    {
        $connectionPoolProphecy = $this->prophesize(ConnectionPool::class);
        GeneralUtility::addInstance(ConnectionPool::class, $connectionPoolProphecy->reveal());
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $connectionPoolProphecy->getQueryBuilderForTable(Argument::cetera())->willReturn($queryBuilderProphecy->reveal());
        $restrictionContainerProphecy = $this->prophesize(QueryRestrictionContainerInterface::class);
        $queryBuilderProphecy->getRestrictions()->willReturn($restrictionContainerProphecy->reveal());
        $restrictionContainerProphecy->removeAll()->shouldBeCalled();
        $queryBuilderProphecy->select('*')->shouldBeCalled()->willReturn($queryBuilderProphecy->reveal());
        $queryBuilderProphecy->from('testtable')->shouldBeCalled()->willReturn($queryBuilderProphecy->reveal());
        $expressionBuilderProphecy = $this->prophesize(ExpressionBuilder::class);
        $queryBuilderProphecy->expr()->willReturn($expressionBuilderProphecy->reveal());
        $queryBuilderProphecy->createNamedParameter(Argument::cetera())->willReturnArgument(0);
        $expressionBuilderProphecy->eq(Argument::cetera())->willReturn('');
        $queryBuilderProphecy->where('')->shouldBeCalled()->willReturn($queryBuilderProphecy->reveal());
        $statementProphecy = $this->prophesize(\Doctrine\DBAL\Statement::class);
        $queryBuilderProphecy->execute()->shouldBeCalled()->willReturn($statementProphecy->reveal());
        $statementProphecy->fetch()->shouldBeCalled()->willReturn([]);

        $subject = new DataHandlerFlushByTagHook();
        $GLOBALS['TCA']['testtable'] = require(__DIR__ . '/Fixtures/tca_with_references.php');
        $this->assertEquals(
            [],
            $this->callInaccessibleMethod($subject, 'findReferencedDatabaseEntries', 'testtable', [], 23)
        );
    }

    /**
     * @test
     */
    public function findReferencedDatabaseEntriesReturnsEmptyArrayForTcaWithRelationsAndNoExistingDatabaseEntryWithReferenceToZeroGiven()
    {
        $connectionPoolProphecy = $this->prophesize(ConnectionPool::class);
        GeneralUtility::addInstance(ConnectionPool::class, $connectionPoolProphecy->reveal());
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $connectionPoolProphecy->getQueryBuilderForTable(Argument::cetera())->willReturn($queryBuilderProphecy->reveal());
        $restrictionContainerProphecy = $this->prophesize(QueryRestrictionContainerInterface::class);
        $queryBuilderProphecy->getRestrictions()->willReturn($restrictionContainerProphecy->reveal());
        $restrictionContainerProphecy->removeAll()->shouldBeCalled();
        $queryBuilderProphecy->select('*')->shouldBeCalled()->willReturn($queryBuilderProphecy->reveal());
        $queryBuilderProphecy->from('testtable')->shouldBeCalled()->willReturn($queryBuilderProphecy->reveal());
        $expressionBuilderProphecy = $this->prophesize(ExpressionBuilder::class);
        $queryBuilderProphecy->expr()->willReturn($expressionBuilderProphecy->reveal());
        $queryBuilderProphecy->createNamedParameter(Argument::cetera())->willReturnArgument(0);
        $expressionBuilderProphecy->eq(Argument::cetera())->willReturn('');
        $queryBuilderProphecy->where('')->shouldBeCalled()->willReturn($queryBuilderProphecy->reveal());
        $statementProphecy = $this->prophesize(\Doctrine\DBAL\Statement::class);
        $queryBuilderProphecy->execute()->shouldBeCalled()->willReturn($statementProphecy->reveal());
        $statementProphecy->fetch()->shouldBeCalled()->willReturn([]);

        $subject = new DataHandlerFlushByTagHook();
        $GLOBALS['TCA']['testtable'] = require(__DIR__ . '/Fixtures/tca_with_references.php');
        $this->assertEquals(
            [],
            $this->callInaccessibleMethod($subject, 'findReferencedDatabaseEntries', 'testtable', ['cust_stuff' =>'0'], 23)
        );
    }

    /**
     * @test
     */
    public function findReferencedDatabaseEntriesReturnsRelationsForTcaWithRelationsAndDbWithNamedTables()
    {
        $connectionPoolProphecy = $this->prophesize(ConnectionPool::class);
        GeneralUtility::addInstance(ConnectionPool::class, $connectionPoolProphecy->reveal());
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $connectionPoolProphecy->getQueryBuilderForTable(Argument::cetera())->willReturn($queryBuilderProphecy->reveal());
        $restrictionContainerProphecy = $this->prophesize(QueryRestrictionContainerInterface::class);
        $queryBuilderProphecy->getRestrictions()->willReturn($restrictionContainerProphecy->reveal());
        $restrictionContainerProphecy->removeAll()->shouldBeCalled();
        $queryBuilderProphecy->select('*')->shouldBeCalled()->willReturn($queryBuilderProphecy->reveal());
        $queryBuilderProphecy->from('testtable')->shouldBeCalled()->willReturn($queryBuilderProphecy->reveal());
        $expressionBuilderProphecy = $this->prophesize(ExpressionBuilder::class);
        $queryBuilderProphecy->expr()->willReturn($expressionBuilderProphecy->reveal());
        $queryBuilderProphecy->createNamedParameter(Argument::cetera())->willReturnArgument(0);
        $expressionBuilderProphecy->eq(Argument::cetera())->willReturn('');
        $queryBuilderProphecy->where('')->shouldBeCalled()->willReturn($queryBuilderProphecy->reveal());
        $statementProphecy = $this->prophesize(\Doctrine\DBAL\Statement::class);
        $queryBuilderProphecy->execute()->shouldBeCalled()->willReturn($statementProphecy->reveal());
        $statementProphecy->fetch()->shouldBeCalled()->willReturn([]);

        $subject = new DataHandlerFlushByTagHook();
        $GLOBALS['TCA']['testtable'] = require(__DIR__ . '/Fixtures/tca_with_references.php');
        $this->assertEquals(
            ['foo_21', 'bar_42'],
            $this->callInaccessibleMethod($subject, 'findReferencedDatabaseEntries', 'testtable', ['cust_stuff' => 'foo_21, bar_42'], 23)
        );
    }

    /**
     * @test
     */
    public function findReferencedDatabaseEntriesReturnsRelationsForTcaWithRelationsAndDbWithNumericalReferences()
    {
        $connectionPoolProphecy = $this->prophesize(ConnectionPool::class);
        GeneralUtility::addInstance(ConnectionPool::class, $connectionPoolProphecy->reveal());
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $connectionPoolProphecy->getQueryBuilderForTable(Argument::cetera())->willReturn($queryBuilderProphecy->reveal());
        $restrictionContainerProphecy = $this->prophesize(QueryRestrictionContainerInterface::class);
        $queryBuilderProphecy->getRestrictions()->willReturn($restrictionContainerProphecy->reveal());
        $restrictionContainerProphecy->removeAll()->shouldBeCalled();
        $queryBuilderProphecy->select('*')->shouldBeCalled()->willReturn($queryBuilderProphecy->reveal());
        $queryBuilderProphecy->from('testtable')->shouldBeCalled()->willReturn($queryBuilderProphecy->reveal());
        $expressionBuilderProphecy = $this->prophesize(ExpressionBuilder::class);
        $queryBuilderProphecy->expr()->willReturn($expressionBuilderProphecy->reveal());
        $queryBuilderProphecy->createNamedParameter(Argument::cetera())->willReturnArgument(0);
        $expressionBuilderProphecy->eq(Argument::cetera())->willReturn('');
        $queryBuilderProphecy->where('')->shouldBeCalled()->willReturn($queryBuilderProphecy->reveal());
        $statementProphecy = $this->prophesize(\Doctrine\DBAL\Statement::class);
        $queryBuilderProphecy->execute()->shouldBeCalled()->willReturn($statementProphecy->reveal());
        $statementProphecy->fetch()->shouldBeCalled()->willReturn([]);

        $subject = new DataHandlerFlushByTagHook();
        $GLOBALS['TCA']['testtable'] = require(__DIR__ . '/Fixtures/tca_with_references.php');
        $this->assertEquals(
            ['fe_users_21', 'fe_users_42'],
            $this->callInaccessibleMethod($subject, 'findReferencedDatabaseEntries', 'testtable', ['cust_fe_user' => '21, 42'], 23)
        );
    }

    /**
     * @test
     */
    public function findReferencedDatabaseEntriesReturnsRelationsForTcaWithMmRelationsAndDb()
    {
        $this->markTestSkipped('Create a functional test for this');

        $subject = new DataHandlerFlushByTagHook();
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
            $this->callInaccessibleMethod($subject, 'findReferencedDatabaseEntries', 'testtable', [], 23)
        );
    }

    /**
     * @test
     */
    public function findReferencedDatabaseEntriesReturnsRelationsForTcaWithMmRelationsDbAndTablenames()
    {
        $this->markTestSkipped('Create a functional test for this');

        $subject = new DataHandlerFlushByTagHook();
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
            $this->callInaccessibleMethod($subject, 'findReferencedDatabaseEntries', 'testtable', [], 23)
        );
    }
}
