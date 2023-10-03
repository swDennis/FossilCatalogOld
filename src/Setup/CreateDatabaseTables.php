<?php

namespace App\Setup;

use App\Entity\InstallationData;
use App\Exceptions\DatabaseDefaultDataException;
use App\Exceptions\DatabaseTableCreationException;
use PDO;

class CreateDatabaseTables implements CreateDatabaseTablesInterface
{
    const SQL_FILE = __DIR__ . '/database.sql';
    const SQL_DEFAULT_DATA_FILE = __DIR__ . '/defaultData.sql';

    public function createDatabaseTables(
        InstallationData $installationData,
        PDO              $connection
    ): void {
        $useDatabaseString = sprintf('USE %s;', $installationData->getDatabaseName());

        $sql = file_get_contents(self::SQL_FILE);
        if (!is_string($sql)) {
            throw new \UnexpectedValueException(sprintf('SQL file is not a string. FILE: %s', self::SQL_FILE));
        }

        $sql = $useDatabaseString . $sql;

        try {
            $connection->exec($sql);
        } catch (\Exception $exception) {
            throw new DatabaseTableCreationException($exception);
        }

        try {
            $sqlDefaultData = file_get_contents(self::SQL_DEFAULT_DATA_FILE);
            if (!is_string($sqlDefaultData)) {
                throw new \UnexpectedValueException(sprintf('SQL file is not a string. FILE: %s', self::SQL_DEFAULT_DATA_FILE));
            }

            $connection->exec($sqlDefaultData);
        } catch (\Exception $exception) {
            if (!str_contains($exception->getMessage(), "Duplicate entry '1' for key 'fossil_form_field.PRIMARY")) {
                throw new DatabaseDefaultDataException($exception);
            }
        }
    }
}