<?php

namespace App\Export\Handler\Exceptions;

use Exception;

class UnexpectedStatusException extends Exception
{
    public function __construct(string $expectedStatus, string $gotStatus)
    {
        parent::__construct(
            sprintf(
                'Expected status of type: %s. Got %s',
                $expectedStatus,
                $gotStatus
            )
        );
    }
}
