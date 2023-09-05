<?php

namespace App\Import;

use Symfony\Component\HttpFoundation\File\File;

interface ImportFileValidatorInterface
{
    public const EXPECTED_FILES_IN_IMPORT = [
        'Category.csv',
        'Fossil.csv',
        'FossilFormField.csv',
        'Image.csv',
        'Tag.csv',
        'TagCategoryRelation.csv',
    ];

    public const EXPECTED_MIME_TYPE = 'application/zip';

    public const FILE_REGEX = '/^[0-9]{2}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])[ ](1[0-2]|0?[1-9])(_)([0-5]?[0-9])(_)([0-5]?[0-9])\.fossilienkatalog.backup.zip$/';

    public function validate(File $file): void;

    public function validateContentStructure(File $file): string;
}