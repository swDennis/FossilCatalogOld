<?php

namespace App\Services\Installation;

use App\Entity\InstallationData;
use App\Exceptions\CannotCreateLockFileException;

class InstallationService implements InstallationServiceInterface
{
    public function checkLockFileExists(): bool
    {
        if (\file_exists(self::LOCKFILE)) {
            return true;
        }

        return false;
    }

    public function createInstallLockFile(): void
    {
        if (!\is_file(self::LOCKFILE)) {
            $dateTime = new \DateTime();
            $content = \sprintf('Installed at: %s', $dateTime->format('Y-m-d H:i:s'));

            \file_put_contents(self::LOCKFILE, $content);
        }

        if (!$this->checkLockFileExists()) {
            throw new CannotCreateLockFileException();
        }
    }

    public function createDonEnvFile(InstallationData $installationData): bool
    {
        $dotEnvContent = \file_get_contents(self::DOT_ENV_DIST);
        if (!is_string($dotEnvContent)) {
            throw new \UnexpectedValueException(sprintf('Cannot read content of %s', self::DOT_ENV_DIST));
        }
        // mysql://root:root@mysql:3306/fossils
        $databaseConnectionString = sprintf(
            'mysql://%s:%s@%s:%s/%s',
            $installationData->getDatabaseUsername(),
            $installationData->getDatabasePassword(),
            $installationData->getDatabaseHost(),
            $installationData->getDatabasePort(),
            $installationData->getDatabaseName()
        );

        $dotEnvContent = \str_replace(self::DATABASE_REPLACE, $databaseConnectionString, $dotEnvContent);
        $dotEnvContent = \str_replace(self::APP_SECRET_REPLACE, $installationData->getAppSecret(), $dotEnvContent);

        \file_put_contents(self::DOT_ENV, $dotEnvContent);

        return \file_exists(self::DOT_ENV);
    }

    public function createAppSecret(): string
    {
        $a = '0123456789abcdef';
        $secret = '';
        for ($i = 0; $i < 32; $i++) {
            $secret .= $a[rand(0, 15)];
        }

        return $secret;
    }
}