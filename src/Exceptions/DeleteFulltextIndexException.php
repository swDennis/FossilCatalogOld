<?php

namespace App\Exceptions;

use Exception;

class DeleteFulltextIndexException extends Exception
{
    public function __construct(string $indexName, string $tableName, Exception $previousException)
    {
        parent::__construct(sprintf('Cannot delete FULLTEXT INDEX %s for %s', $indexName, $tableName), 0, $previousException);
    }
}