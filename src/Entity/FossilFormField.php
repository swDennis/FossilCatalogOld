<?php

namespace App\Entity;

use App\Form\FormBuilder\FormFieldType;

class FossilFormField extends AbstractStruct
{
    protected ?int $id = null;

    protected ?int $fieldOrder = null;

    protected ?string $fieldName = null;

    protected ?string $fieldLabel = null;

    protected ?string $fieldType = FormFieldType::TEXT;

    protected bool $showInOverview = false;

    protected bool $allowBlank = true;

    protected bool $isFilter = false;

    protected bool $isRequiredDefault = false;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getFieldOrder(): ?int
    {
        return $this->fieldOrder;
    }

    /**
     * @param int|null $fieldOrder
     */
    public function setFieldOrder(?int $fieldOrder): void
    {
        $this->fieldOrder = $fieldOrder;
    }

    /**
     * @return string
     */
    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }

    /**
     * @param string $fieldName
     */
    public function setFieldName(?string $fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return string
     */
    public function getFieldLabel(): ?string
    {
        return $this->fieldLabel;
    }

    /**
     * @param string $fielLabel
     */
    public function setFieldLabel(?string $fieldLabel): void
    {
        $this->fieldLabel = $fieldLabel;
    }

    /**
     * @return string
     */
    public function getFieldType(): ?string
    {
        return $this->fieldType;
    }

    /**
     * @param string $fieldType
     */
    public function setFieldType(?string $fieldType): void
    {
        $this->fieldType = $fieldType;
    }

    /**
     * @return bool
     */
    public function getShowInOverview(): bool
    {
        return $this->showInOverview;
    }

    /**
     * @param bool $showInOverview
     */
    public function setShowInOverview(bool $showInOverview): self
    {
        $this->showInOverview = $showInOverview;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsFilter(): bool
    {
        return $this->isFilter;
    }

    /**
     * @param bool $isFilter
     */
    public function setIsFilter(bool $isFilter): void
    {
        $this->isFilter = $isFilter;
    }

    /**
     * @return bool
     */
    public function getAllowBlank(): bool
    {
        return $this->allowBlank;
    }

    /**
     * @param bool $allowBlank
     */
    public function setAllowBlank(bool $allowBlank): void
    {
        $this->allowBlank = $allowBlank;
    }

    /**
     * @return bool
     */
    public function getIsRequiredDefault(): bool
    {
        return $this->isRequiredDefault;
    }

    public function setIsRequiredDefault(bool $isRequiredDefault): void
    {
        $this->isRequiredDefault = $isRequiredDefault;
    }
}