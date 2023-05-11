<?php

namespace App\Command;

use App\Entity\InstallationData;
use App\Exceptions\CreateUserException;
use App\Exceptions\DatabaseCreationException;
use App\Exceptions\DatabaseExistsException;
use App\Services\Installation\CreateUserServiceInterface;
use App\Services\Installation\InstallationConnection;
use App\Services\Installation\InstallationService;
use App\Setup\CreateDatabase;
use App\Setup\CreateDatabaseInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-database',
    description: 'Creates only the Fossil Database and requires the migrations after',
    hidden: false,
    aliases: ['app:install-database']
)]
class SetupCommand extends Command
{
    protected static $defaultName = 'app:create-database';

    protected static $defaultDescription = 'Creates only the Fossil Database and requires the migrations after';

    private CreateDatabase $createDatabase;

    private UserPasswordHasherInterface $passwordHasher;

    private EntityManagerInterface $entityManager;

    private CreateUserServiceInterface $createUserService;

    public function __construct(
        CreateDatabaseInterface $createDatabase,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        CreateUserServiceInterface $createUserService
    ) {
        $this->createDatabase = $createDatabase;

        parent::__construct();
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->createUserService = $createUserService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $databaseName = $io->ask('Please enter the database name', 'fossils');

        $databaseUserName = $io->ask('Please enter the database user name', 'root');

        $databasePassword = $io->askHidden('Please enter the database password');

        $databaseHostName = $io->ask('Please enter the database host name', 'mysql');

        $databasePort = $io->ask('Please enter the database port', '3306');

        $installationService = new InstallationService();
        $installationData = new InstallationData();
        $installationData->fromArray([
            'databaseName' => $databaseName,
            'databaseUsername' => $databaseUserName,
            'databasePassword' => $databasePassword,
            'databaseHost' => $databaseHostName,
            'databasePort' => $databasePort,
            'appSecret' => $installationService->createAppSecret(),
        ]);

        $installationConnection = new InstallationConnection();

        try {
            $this->createDatabase->createDatabase($installationData, $installationConnection->createPDOConnection($installationData));
        } catch (DatabaseExistsException $databaseExistsException) {
            // Do nothing
        } catch (DatabaseCreationException $databaseCreationException) {
            return $this->handleException($databaseCreationException, $io);
        }

        $userEmail = $io->ask('Please enter your E-Mail address', null, function ($answer) {
            if (!\filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('E-Mail is not valid');
            }

            return $answer;
        });

        try {
            $installationService->createDonEnvFile($installationData);
        } catch (Exception $exception) {
            return $this->handleException($exception, $io);
        }

        $userPassword = $io->askHidden('Please enter a password for login');

        $user = $this->createUserService->createUser($userEmail, $userPassword);
        try {
            $this->createUserService->saveUser($user);

            $io->info('User successfully created');
        } catch (CreateUserException $exception) {
            return $this->handleException($exception, $io);
        }

        try {
            $installationService->createInstallLockFile();
        } catch (Exception $exception) {
            return $this->handleException($exception, $io);
        }

        return Command::SUCCESS;
    }

    private function handleException(Exception $exception, SymfonyStyle $io): int
    {
        $io->error($exception->getMessage());
        $io->error($exception->getTraceAsString());

        $previousException = $exception->getPrevious();
        if ($previousException instanceof Exception) {
            $io->error($exception->getPrevious()->getMessage());
            $io->error($exception->getPrevious()->getTraceAsString());
        }

        return Command::FAILURE;
    }
}
