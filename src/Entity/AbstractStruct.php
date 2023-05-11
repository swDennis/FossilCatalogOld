<?php

namespace App\Entity;

abstract class AbstractStruct
{
    public function fromArray(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}