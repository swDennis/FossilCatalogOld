<?php

namespace App\Validator\TagName;

use Symfony\Component\Validator\Constraint;

class TagNameConstraint extends Constraint
{
    public string $message = 'The Tag with name "{{ string }}" already exists';

    public string $mode = 'strict';

    public TagInfo $tagInfo;

    public function __construct(TagInfo $tagInfo, mixed $options = null, array $groups = null, $payload = null)
    {
        parent::__construct($options, $groups, $payload);

        $this->tagInfo = $tagInfo;
    }

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}