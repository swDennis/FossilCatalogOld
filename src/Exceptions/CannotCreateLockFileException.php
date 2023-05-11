<?php

namespace App\Exceptions;

use App\Services\Installation\InstallationServiceInterface;
use Exception;

class CannotCreateLockFileException extends Exception
{
    public function __construct()
    {
        parent::__construct(\sprintf('Cannot create lock file: %s', InstallationServiceInterface::LOCKFILE));
    }
}