<?php

namespace App\Tests\FossilForm;

use App\Services\FossilForm\FossilFormEntityDatabaseCreator;
use App\Tests\Traits\FossilFormFieldRepositoryInterfaceMockTrait;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FossilFormEntityDatabaseCreatorTest extends WebTestCase
{
    use FossilFormFieldRepositoryInterfaceMockTrait;

    private Connection $connection;

    /**
     * @before
     */
    public function before()
    {
        self::bootKernel();

        $this->connection = self::$kernel->getContainer()->get('doctrine')->getConnection();
    }

    public function testAddDatabaseColumns()
    {
        $this->deleteTable();
        $this->createTableForTestCase();

        $fossilFormEntityDatabaseCreator = $this->createFossilFormEntityDatabaseCreator();

        $fossilFormEntityDatabaseCreator->addDatabaseColumns();

        $testInsertStatement = 'INSERT INTO fossil_entity (`findingDate`, `fossilNumber`, `fossilName`, `findingPlace`, `findingLayer`, `earthAge`, `descriptionAndNotes`) VALUES (?, ?, ?, ?, ?, ?, ?)';

        $this->connection->executeQuery($testInsertStatement, [
            '2023-06-30',
            '0000001',
            'Sciponoceras baculoides',
            'Rheine',
            'rhotomagense-Zone',
            'Cenoman',
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.',
        ]);

        $lastInsertId = $this->connection->lastInsertId();

        $result = $this->connection->fetchAssociative('SELECT * FROM `fossil_entity` WHERE id = ?;', [$lastInsertId]);

        static::assertArrayHasKey('id', $result);
        static::assertArrayHasKey('showInOverview', $result);
        static::assertArrayHasKey('findingDate', $result);
        static::assertArrayHasKey('fossilNumber', $result);
        static::assertArrayHasKey('fossilName', $result);
        static::assertArrayHasKey('findingPlace', $result);
        static::assertArrayHasKey('findingLayer', $result);
        static::assertArrayHasKey('earthAge', $result);
        static::assertArrayHasKey('descriptionAndNotes', $result);
    }

    private function deleteTable(): void
    {
        $sql = 'DROP TABLE IF EXISTS fossil_entity;';

        $this->connection->executeQuery($sql);
    }

    private function createTableForTestCase(): void
    {
        $sql = '
            CREATE TABLE IF NOT EXISTS fossil_entity
            (
                id BIGINT AUTO_INCREMENT NOT NULL,
                showInOverview BOOLEAN NOT NULL DEFAULT false,
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
              COLLATE `utf8mb4_unicode_ci`
              ENGINE = InnoDB;
        ';

        $this->connection->executeQuery($sql);
    }

    private function createFossilFormEntityDatabaseCreator(): FossilFormEntityDatabaseCreator
    {
        return new FossilFormEntityDatabaseCreator(
            $this->createFossilFormFieldRepositoryInterfaceMock(),
            $this->connection
        );
    }
}