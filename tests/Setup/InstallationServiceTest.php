<?php
//
//namespace App\Tests\Setup;
//
//use App\Entity\InstallationData;
//use App\Services\Installation\InstallationService;
//use App\Services\Installation\InstallationServiceInterface;
//use PHPUnit\Framework\TestCase;
//
//class InstallationServiceTest extends TestCase
//{
//    private $lockFileExists = false;
//
//    /**
//     * @before
//     */
//    public function checkLockFileExists(): void
//    {
//        $installationService = $this->createInstallationService();
//        $this->lockFileExists = $installationService->checkLockFileExists();
//    }
//
//    /**
//     * @after
//     */
//    public function setLockFileIfRequired(): void
//    {
//        $installationService = $this->createInstallationService();
//        if ($this->lockFileExists) {
//            $installationService->createInstallLockFile();
//        }
//    }
//
//    public function testCheckLockFileExists(): void
//    {
//        $installationService = $this->createInstallationService();
//        $installationService->createInstallLockFile();
//
//        static::assertTrue($installationService->checkLockFileExists());
//
//        \unlink(InstallationServiceInterface::LOCKFILE);
//
//        static::assertFalse($installationService->checkLockFileExists());
//    }
//
//    public function testCreateInstallLockFile(): void
//    {
//        $this->createInstallationService()->createInstallLockFile();
//
//        static::assertFileExists(InstallationServiceInterface::LOCKFILE);
//
//        \unlink(InstallationServiceInterface::LOCKFILE);
//
//        static::assertFileDoesNotExist(InstallationServiceInterface::LOCKFILE);
//    }
//
//    public function testCreateDonEnvFile(): void
//    {
//        $installationData = new InstallationData();
//        $installationData->fromArray([
//            'databaseName' => '__DB_NAME__',
//            'databaseUsername' => '__DB_USER_NAME__',
//            'databasePassword' => '__DB_PASSWORD__',
//            'databaseHost' => '__DB_HOST__',
//            'databasePort' => '__DB_PORT__',
//            'appSecret' => '__NEW_APP_SECRET__',
//        ]);
//
//        $this->createInstallationService()->createDonEnvFile($installationData);
//
//        $result = \file_get_contents(InstallationServiceInterface::DOT_ENV);
//
//        \unlink(InstallationServiceInterface::DOT_ENV);
//        static::assertFileDoesNotExist(InstallationServiceInterface::DOT_ENV);
//
//        static::assertStringContainsString('__DB_NAME__', $result);
//        static::assertStringContainsString('__DB_USER_NAME__', $result);
//        static::assertStringContainsString('__DB_PASSWORD__', $result);
//        static::assertStringContainsString('__DB_HOST__', $result);
//        static::assertStringContainsString('__DB_PORT__', $result);
//        static::assertStringContainsString('__NEW_APP_SECRET__', $result);
//    }
//
//    private function createInstallationService(): InstallationService
//    {
//        return new InstallationService();
//    }
//}