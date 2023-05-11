<?php

namespace App\Services\Installation;

use App\Entity\InstallationData;

interface InstallationServiceInterface
{
    const LOCKFILE = __DIR__ . '/../../../install.lock';

    const DOT_ENV_DIST = __DIR__ . '/../../../.env.dist';

    const DOT_ENV = __DIR__ . '/../../../.env';

    const DATABASE_SQL_FILE = __DIR__ .  '/../../Setup/database.sql';

    const DATABASE_REPLACE = '___DATABASE_STRING___';

    const APP_SECRET_REPLACE = '___APP_SECRET___';

    const DATABASE_NAME_REPLACE = '___DATABASE_NAME___';

    public function checkLockFileExists(): bool;

    public function createInstallLockFile(): void;

    public function createDonEnvFile(InstallationData $installationData): bool;
}
