<?php

namespace App\Exceptions;

use Exception;

class CreateUserException extends Exception
{
    public function __construct(string $email, Exception $previousException)
    {
        parent::__construct(sprintf('Cannot create user for %s', $email), 0, $previousException);
    }
}