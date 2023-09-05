<?php

namespace App\Setup;

use App\Entity\InstallationData;
use App\Exceptions\DatabaseCreationException;
use App\Exceptions\DatabaseExistsException;
use PDO;

class CreateDatabase implements CreateDatabaseInterface
{
    public function createDatabase(InstallationData $installationData, PDO $connection): void
    {
        if ($this->checkIfDatabaseExists($connection, $installationData->getDatabaseName())) {
            throw new DatabaseExistsException($installationData->getDatabaseName());
        }

        $sql = sprintf('CREATE DATABASE %s;', $installationData->getDatabaseName());

        $connection->exec($sql);

        if (!$this->checkIfDatabaseExists($connection, $installationData->getDatabaseName())) {
            throw new DatabaseCreationException($installationData->getDatabaseName());
        }
    }

    private function checkIfDatabaseExists(PDO $connection, string $databaseName): bool
    {
        $sql = sprintf('SHOW DATABASES LIKE "%s";', $databaseName);

        $result = $connection->query($sql)->fetchColumn();

        if (is_string($result)) {
            return true;
        }

        return false;
    }
}
