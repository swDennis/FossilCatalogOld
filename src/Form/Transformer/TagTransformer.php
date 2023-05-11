<?php

namespace App\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class TagTransformer implements DataTransformerInterface
{
    public function transform(mixed $data): mixed
    {
        $return = [];
        foreach ($data as $datum) {
            $return[] = $datum['id'];
        }
        return $return;
    }

    public function reverseTransform(mixed $data): mixed
    {
        return [$data];
    }
}
