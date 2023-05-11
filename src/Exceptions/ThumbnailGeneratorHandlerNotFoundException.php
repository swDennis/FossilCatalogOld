<?php

namespace App\Exceptions;

class ThumbnailGeneratorHandlerNotFoundException extends \Exception
{
    public function __construct(string $mimeType)
    {
        parent::__construct(sprintf('Cannot find handler for mime type %s', $mimeType), 0);
    }
}