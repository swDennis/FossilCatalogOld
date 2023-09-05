<?php

namespace App\Setup;

use App\Entity\InstallationData;
use App\Services\FossilForm\FossilFormEntityDatabaseCreator;
use PDO;

interface CreateDatabaseTablesInterface
{
    public function createDatabaseTables(
        InstallationData $installationData,
        PDO $connection
    ): void;
}