<?php

namespace App\Import\Exception;

use Exception;

class FileNotFoundException extends Exception
{
    public function __construct(string $expectedFile)
    {
        parent::__construct(sprintf('Cannot find file %s in uploaded backup', $expectedFile));
    }
}