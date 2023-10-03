<?php

namespace App\Exceptions;

use Exception;

class IsNotNumericException extends Exception
{
    public function __construct(object $object)
    {
        parent::__construct(sprintf('Value is not numeric in %s', get_class($object)));
    }
}