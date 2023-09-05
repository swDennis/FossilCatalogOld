<?php

namespace App\Services\Installation;

use App\Entity\InstallationData;
use PDO;

class InstallationConnection implements InstallationConnectionInterface
{
    public function createPDOConnection(InstallationData $installationData): PDO
    {
        $connectionString = sprintf('mysql:host=%s;port=%s;', $installationData->getDatabaseHost(), $installationData->getDatabasePort());

        return new PDO(
            $connectionString,
            $installationData->getDatabaseUsername(),
            $installationData->getDatabasePassword(),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            ]
        );
    }
}