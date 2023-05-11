<?php

namespace App\Import\Exception;

use App\Import\ImportFileValidatorInterface;
use Exception;

class UnexpectedMimeTypeException extends Exception
{
    public function __construct(string $mimeType)
    {
        parent::__construct(
            sprintf(
                'Expect file with mime type of: %s got: %s',
                ImportFileValidatorInterface::EXPECTED_MIME_TYPE,
                $mimeType
            )
        );
    }
}