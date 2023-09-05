<?php

namespace App\Services\Installation;

use App\Entity\InstallationData;
use PDO;

interface InstallationConnectionInterface
{
    public function createPDOConnection(InstallationData $installationData): PDO;
}
