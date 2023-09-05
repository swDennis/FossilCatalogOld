<?php

namespace App\Export\Exceptions;

use Exception;

class CreateZipException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}