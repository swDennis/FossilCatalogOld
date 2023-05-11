<?php

namespace App\Setup;

use App\Entity\InstallationData;
use PDO;

interface CreateDatabaseInterface
{
    public function createDatabase(InstallationData $installationData, PDO $connection): void;
}