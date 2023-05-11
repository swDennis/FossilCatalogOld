<?php

namespace App\Validator\MysqlKeyword;

use Symfony\Component\Validator\Constraint;

class MysqlKeywordConstraint extends Constraint
{
    public string $message = 'The string "{{ string }}" is an Mysql keyword.';

    public string $mode = 'strict';

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}