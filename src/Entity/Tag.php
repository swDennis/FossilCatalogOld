<?php


namespace App\Entity;

class Tag extends AbstractStruct
{
    protected ?int $id = null;

    protected ?string $name = null;

    protected ?bool $isUsedAsCategory = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getIsUsedAsCategory(): ?bool
    {
        return $this->isUsedAsCategory;
    }

    public function setIsUsedAsCategory(?bool $isUsedAsCategory): void
    {
        $this->isUsedAsCategory = $isUsedAsCategory;
    }

}