<?php

namespace App\Exceptions;

class DatabaseDefaultDataException extends \Exception
{
    public function __construct(\Exception $previousException)
    {
        parent::__construct('Cannot install default data', 0, $previousException);
    }
}