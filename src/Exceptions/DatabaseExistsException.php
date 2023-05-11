<?php

namespace App\Exceptions;

class DatabaseExistsException extends \Exception
{
    public function __construct(string $databaseName)
    {
        parent::__construct(sprintf('Database %s already Exists', $databaseName));
    }
}