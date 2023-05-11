<?php

namespace App\Exceptions;

use Exception;

class CreateFulltextIndexException extends Exception
{
    public function __construct(string $indexName, string $tableName, Exception $previousException)
    {
        parent::__construct(sprintf('Cannot create FULLTEXT INDEX %s for %s', $indexName, $tableName), 0, $previousException);
    }
}