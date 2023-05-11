<?php

namespace App\Import\Exception;

use Exception;

class UnexpectedFileNameException extends Exception
{
    public function __construct()
    {
        parent::__construct('Expect file with specific format like: 23-08-22 05_36_59.fossilienkatalog.backup.zip');
    }
}