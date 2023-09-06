<?php

namespace App\Repository;

use App\Entity\FossilFormField;

interface FossilFormFieldRepositoryInterface
{
    public const FORM_FIELD_TABLE_NAME = 'fossil_form_field';
    public const FORM_FIELD_COLUMN_FIELD_ID = 'id';
    public const FORM_FIELD_COLUMN_FIELD_ORDER = 'fieldOrder';
    public const FORM_FIELD_COLUMN_FIELD_NAME = 'fieldName';
    public const FORM_FIELD_COLUMN_FIELD_LABEL = 'fieldLabel';
    public const FORM_FIELD_COLUMN_FIELD_TYPE = 'fieldType';
    public const FORM_FIELD_COLUMN_FIELD_SHOW_IN_OVERVIEW = 'showInOverview';
    public const FORM_FIELD_COLUMN_FIELD_ALLOW_BLANK = 'allowBlank';
    public const FORM_FIELD_COLUMN_FIELD_IS_FILTER = 'isFilter';
    public const FORM_FIELD_COLUMN_IS_REQUIRED_DEFAULT = 'isRequiredDefault';

    public function saveFossilFormField(FossilFormField $formField): FossilFormField;

    public function getFossilFormFieldList(): array;

    public function getFossilFormFieldListForOverview(): array;

    public function deleteFossilFormField(int $id): void;

    public function getFossilFormFieldById(int $id): array;

    public function getFilterableFields(): array;

    public function getFilterableFieldsPopulatedWithValues(): array;

    public function getNewOrderNumber(): int;

    public function getExportList(int $limit, int $offset): array;

    public function getColumnCount(): int;
}