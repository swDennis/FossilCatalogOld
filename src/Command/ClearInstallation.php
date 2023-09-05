<?php

namespace App\Command;

use App\Services\Installation\InstallationServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:clear-installation',
    description: 'Prepares app for a new installation: Removes .env and install.lock file',
    hidden: false,
    aliases: ['app:clear-installation']
)]
class ClearInstallation extends Command
{
    protected static $defaultName = 'app:clear-installation';

    protected static $defaultDescription = 'Prepares app for a new installation: Removes .env and install.lock file';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (file_exists(InstallationServiceInterface::LOCKFILE)) {
            unlink(InstallationServiceInterface::LOCKFILE);
        }

        if (file_exists(InstallationServiceInterface::DOT_ENV)) {
            unlink(InstallationServiceInterface::DOT_ENV);
        }

        return Command::SUCCESS;
    }
}