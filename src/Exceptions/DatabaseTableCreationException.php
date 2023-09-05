<?php

namespace App\Exceptions;

class DatabaseTableCreationException extends \Exception
{
    public function __construct(\Exception $previousException)
    {
        parent::__construct('Cannot create DatabaseTables', 0, $previousException);
    }
}