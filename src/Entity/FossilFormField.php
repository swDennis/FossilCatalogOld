<?php

namespace App\Entity;

use App\Form\FormBuilder\FormFieldType;

class FossilFormField extends AbstractStruct
{
    protected ?int $id = null;

    protected int $fieldOrder;

    protected string $fieldName;

    protected string $fieldLabel;

    protected string $fieldType = FormFieldType::TEXT;


    protected bool $showInOverview = false;

    protected bool $allowBlank = true;

    protected bool $isFilter = false;

    protected bool $isRequiredDefault = false;

    protected string $value = '';

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getFieldOrder(): int
    {
        return $this->fieldOrder;
    }

    public function setFieldOrder(int $fieldOrder): void
    {
        $this->fieldOrder = $fieldOrder;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function setFieldName(string $fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    public function getFieldLabel(): string
    {
        return $this->fieldLabel;
    }

    public function setFieldLabel(string $fieldLabel): void
    {
        $this->fieldLabel = $fieldLabel;
    }

    public function getFieldType(): string
    {
        return $this->fieldType;
    }

    public function setFieldType(string $fieldType): void
    {
        $this->fieldType = $fieldType;
    }

    public function getShowInOverview(): bool
    {
        return $this->showInOverview;
    }

    public function setShowInOverview(bool $showInOverview): self
    {
        $this->showInOverview = $showInOverview;

        return $this;
    }

    public function getIsFilter(): bool
    {
        return $this->isFilter;
    }

    public function setIsFilter(bool $isFilter): void
    {
        $this->isFilter = $isFilter;
    }

    public function getAllowBlank(): bool
    {
        return $this->allowBlank;
    }

    public function setAllowBlank(bool $allowBlank): void
    {
        $this->allowBlank = $allowBlank;
    }

    public function getIsRequiredDefault(): bool
    {
        return $this->isRequiredDefault;
    }

    public function setIsRequiredDefault(bool $isRequiredDefault): void
    {
        $this->isRequiredDefault = $isRequiredDefault;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}