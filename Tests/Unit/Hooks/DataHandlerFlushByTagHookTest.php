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

use Doctrine\DBAL\Result;
use Doctrine\DBAL\Statement;
use Lolli\Enetcache\Hooks\DataHandlerFlushByTagHook;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionContainerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @todo: These tests should be migrated to functional tests.
 */
class DataHandlerFlushByTagHookTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    /**
     * Helper function to call protected or private methods
     *
     * @param object $object The object to be invoked
     * @param string $name the name of the method to call
     * @param mixed $arguments
     * @return mixed
     */
    protected function callInaccessibleMethod($object, $name, ...$arguments)
    {
        $reflectionObject = new \ReflectionObject($object);
        $reflectionMethod = $reflectionObject->getMethod($name);
        $reflectionMethod->setAccessible(true);
        return $reflectionMethod->invokeArgs($object, $arguments);
    }

    /**
     * @test
     */
    public function findReferencedDatabaseEntriesReturnsEmptyArrayForTcaWithoutRelations()
    {
        $connectionPoolMock = $this->createMock(ConnectionPool::class);
        GeneralUtility::addInstance(ConnectionPool::class, $connectionPoolMock);
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $connectionPoolMock->expects($this->atLeastOnce())->method('getQueryBuilderForTable')->willReturn($queryBuilderMock);
        $restrictionContainerMock = $this->createMock(QueryRestrictionContainerInterface::class);
        $queryBuilderMock->expects($this->any())->method('getRestrictions')->willReturn($restrictionContainerMock);
        $restrictionContainerMock->expects($this->atLeastOnce())->method('removeAll');
        $queryBuilderMock->expects($this->atLeastOnce())->method('select')->with('*')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->atLeastOnce())->method('from')->with('testtable')->willReturn($queryBuilderMock);
        $expressionBuilderMock = $this->createMock(ExpressionBuilder::class);
        $queryBuilderMock->expects($this->any())->method('expr')->willReturn($expressionBuilderMock);
        $queryBuilderMock->expects($this->any())->method('createNamedParameter')->willReturn('23');
        $expressionBuilderMock->expects($this->any())->method('eq')->willReturn('');
        $queryBuilderMock->expects($this->atLeastOnce())->method('where')->willReturn($queryBuilderMock);
        $resultMock = $this->createMock(Result::class);
        $queryBuilderMock->expects($this->atLeastOnce())->method('executeQuery')->willReturn($resultMock);
        $resultMock->expects($this->atLeastOnce())->method('fetchOne')->willReturn([]);
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
        $connectionPoolMock = $this->createMock(ConnectionPool::class);
        GeneralUtility::addInstance(ConnectionPool::class, $connectionPoolMock);
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $connectionPoolMock->expects($this->atLeastOnce())->method('getQueryBuilderForTable')->willReturn($queryBuilderMock);
        $restrictionContainerMock = $this->createMock(QueryRestrictionContainerInterface::class);
        $queryBuilderMock->expects($this->any())->method('getRestrictions')->willReturn($restrictionContainerMock);
        $restrictionContainerMock->expects($this->atLeastOnce())->method('removeAll');
        $queryBuilderMock->expects($this->atLeastOnce())->method('select')->with('*')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->atLeastOnce())->method('from')->with('testtable')->willReturn($queryBuilderMock);
        $expressionBuilderMock = $this->createMock(ExpressionBuilder::class);
        $queryBuilderMock->expects($this->any())->method('expr')->willReturn($expressionBuilderMock);
        $queryBuilderMock->expects($this->any())->method('createNamedParameter')->willReturn('23');
        $expressionBuilderMock->expects($this->any())->method('eq')->willReturn('');
        $queryBuilderMock->expects($this->atLeastOnce())->method('where')->willReturn($queryBuilderMock);
        $resultMock = $this->createMock(Result::class);
        $queryBuilderMock->expects($this->atLeastOnce())->method('executeQuery')->willReturn($resultMock);
        $resultMock->expects($this->atLeastOnce())->method('fetchOne')->willReturn(['cust_fe_user' => '', 'cust_stuff' => '']);
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
        $connectionPoolMock = $this->createMock(ConnectionPool::class);
        GeneralUtility::addInstance(ConnectionPool::class, $connectionPoolMock);
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $connectionPoolMock->expects($this->atLeastOnce())->method('getQueryBuilderForTable')->willReturn($queryBuilderMock);
        $restrictionContainerMock = $this->createMock(QueryRestrictionContainerInterface::class);
        $queryBuilderMock->expects($this->any())->method('getRestrictions')->willReturn($restrictionContainerMock);
        $restrictionContainerMock->expects($this->atLeastOnce())->method('removeAll');
        $queryBuilderMock->expects($this->atLeastOnce())->method('select')->with('*')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->atLeastOnce())->method('from')->with('testtable')->willReturn($queryBuilderMock);
        $expressionBuilderMock = $this->createMock(ExpressionBuilder::class);
        $queryBuilderMock->expects($this->any())->method('expr')->willReturn($expressionBuilderMock);
        $queryBuilderMock->expects($this->any())->method('createNamedParameter')->willReturn('23');
        $expressionBuilderMock->expects($this->any())->method('eq')->willReturn('');
        $queryBuilderMock->expects($this->atLeastOnce())->method('where')->willReturn($queryBuilderMock);
        $resultMock = $this->createMock(Result::class);
        $queryBuilderMock->expects($this->atLeastOnce())->method('executeQuery')->willReturn($resultMock);
        $resultMock->expects($this->atLeastOnce())->method('fetchOne')->willReturn(['cust_fe_user' => '', 'cust_stuff' => '']);
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
        $connectionPoolMock = $this->createMock(ConnectionPool::class);
        GeneralUtility::addInstance(ConnectionPool::class, $connectionPoolMock);
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $connectionPoolMock->expects($this->atLeastOnce())->method('getQueryBuilderForTable')->willReturn($queryBuilderMock);
        $restrictionContainerMock = $this->createMock(QueryRestrictionContainerInterface::class);
        $queryBuilderMock->expects($this->any())->method('getRestrictions')->willReturn($restrictionContainerMock);
        $restrictionContainerMock->expects($this->atLeastOnce())->method('removeAll');
        $queryBuilderMock->expects($this->atLeastOnce())->method('select')->with('*')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->atLeastOnce())->method('from')->with('testtable')->willReturn($queryBuilderMock);
        $expressionBuilderMock = $this->createMock(ExpressionBuilder::class);
        $queryBuilderMock->expects($this->any())->method('expr')->willReturn($expressionBuilderMock);
        $queryBuilderMock->expects($this->any())->method('createNamedParameter')->willReturn('23');
        $expressionBuilderMock->expects($this->any())->method('eq')->willReturn('');
        $queryBuilderMock->expects($this->atLeastOnce())->method('where')->willReturn($queryBuilderMock);
        $resultMock = $this->createMock(Result::class);
        $queryBuilderMock->expects($this->atLeastOnce())->method('executeQuery')->willReturn($resultMock);
        $resultMock->expects($this->atLeastOnce())->method('fetchOne')->willReturn(['cust_fe_user' => '', 'cust_stuff' => '']);
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
        $connectionPoolMock = $this->createMock(ConnectionPool::class);
        GeneralUtility::addInstance(ConnectionPool::class, $connectionPoolMock);
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $connectionPoolMock->expects($this->atLeastOnce())->method('getQueryBuilderForTable')->willReturn($queryBuilderMock);
        $restrictionContainerMock = $this->createMock(QueryRestrictionContainerInterface::class);
        $queryBuilderMock->expects($this->any())->method('getRestrictions')->willReturn($restrictionContainerMock);
        $restrictionContainerMock->expects($this->atLeastOnce())->method('removeAll');
        $queryBuilderMock->expects($this->atLeastOnce())->method('select')->with('*')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->atLeastOnce())->method('from')->with('testtable')->willReturn($queryBuilderMock);
        $expressionBuilderMock = $this->createMock(ExpressionBuilder::class);
        $queryBuilderMock->expects($this->any())->method('expr')->willReturn($expressionBuilderMock);
        $queryBuilderMock->expects($this->any())->method('createNamedParameter')->willReturn('23');
        $expressionBuilderMock->expects($this->any())->method('eq')->willReturn('');
        $queryBuilderMock->expects($this->atLeastOnce())->method('where')->willReturn($queryBuilderMock);
        $resultMock = $this->createMock(Result::class);
        $queryBuilderMock->expects($this->atLeastOnce())->method('executeQuery')->willReturn($resultMock);
        $resultMock->expects($this->atLeastOnce())->method('fetchOne')->willReturn(['cust_fe_user' => '', 'cust_stuff' => '']);
        $subject = new DataHandlerFlushByTagHook();
        $GLOBALS['TCA']['testtable'] = require(__DIR__ . '/Fixtures/tca_with_references.php');
        $this->assertEquals(
            ['fe_users_21', 'fe_users_42'],
            $this->callInaccessibleMethod($subject, 'findReferencedDatabaseEntries', 'testtable', ['cust_fe_user' => '21, 42'], 23)
        );
    }
}
