<?php

namespace App\Import\Exception;

use Exception;

class CannotOpenZipException extends Exception
{
    public function __construct()
    {
        parent::__construct('Cannot open backup file');
    }
}