<?php

namespace App\Services\FossilForm;

class MysqlKeyWordFilter implements MysqlKeyWordFilterInterface
{
    public function isMysqlKeyword(string $columnName): bool
    {
        return in_array(strtoupper($columnName), self::KEY_WORDS);
    }
}