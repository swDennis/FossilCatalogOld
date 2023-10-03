<?php

namespace App\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

/** @phpstan-ignore-next-line */
class TagTransformer implements DataTransformerInterface
{
    public function transform(mixed $data): mixed
    {
        $return = [];

        if (!is_array($data)) {
            return $return;
        }

        foreach ($data as $datum) {
            if (!array_key_exists('id', $datum)) {
                continue;
            }

            $return[] = $datum['id'];
        }

        return $return;
    }

    public function reverseTransform(mixed $data): mixed
    {
        return [$data];
    }
}
