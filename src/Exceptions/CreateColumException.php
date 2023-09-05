<?php

namespace App\Exceptions;

use Exception;

class CreateColumException extends Exception
{
    public function __construct(string $columnName, string $tableName, ?Exception $previousException = null)
    {
        parent::__construct(sprintf('Cannot create column %s for %s', $columnName, $tableName), 0, $previousException);
    }
}