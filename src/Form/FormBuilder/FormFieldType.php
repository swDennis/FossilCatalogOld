<?php

namespace App\Form\FormBuilder;

final class FormFieldType
{
    public const TEXT = 'text';

    public const TEXT_AREA = 'textarea';

    public const NUMBER = 'number';

    public const DATE = 'date';

    private function __construct()
    {

    }

    /**
     * @return array<int, string>
     */
    public function getList(): array
    {
        return [
            self::TEXT,
            self::TEXT_AREA,
            self::NUMBER,
            self::DATE,
        ];
    }
}