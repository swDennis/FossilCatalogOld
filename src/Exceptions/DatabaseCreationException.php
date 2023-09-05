<?php

namespace App\Exceptions;

class DatabaseCreationException extends \Exception
{
    public function __construct(string $databaseName)
    {
        parent::__construct(sprintf('Cannot create %s database', $databaseName));
    }
}