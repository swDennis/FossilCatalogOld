<?php

namespace App\Entity;

class FossilEntity extends AbstractStruct 
{
    protected ?int $id = null;
    
    protected bool $showInOverview = false;
    
    protected array $images = [];
    
    protected array $categories = [];
    
    protected array $tags = [];
    
    protected ?string $findingDate = null;

	protected ?string $fossilNumber = null;

	protected ?string $fossilGenus = null;

	protected ?string $fossilSpecies = null;

	protected ?string $findingPlace = null;

	protected ?string $findingLayer = null;

	protected ?string $earthAge = null;

	protected ?string $descriptionAndNotes = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }
    
    public function getShowInOverview(): bool
    {
        return $this->showInOverview;
    }

    public function setShowInOverview(bool $showInOverview): void
    {
        $this->showInOverview = $showInOverview;
    }
    
    public function getImages(): array
    {
        return $this->images;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }
    
    public function getTags(): array
    {
        return $this->tags;
    }

    public function setImages(array $images): void
    {
        $this->images = $images;
    }
    
    public function addImage(Image $image): void
    {
        $this->images[] = $image;
    }
    
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }
    
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }
    
    public function getFindingDate(): ?string
	{
 		return $this->findingDate;
	}

	public function getFossilNumber(): ?string
	{
 		return $this->fossilNumber;
	}

	public function getFossilGenus(): ?string
	{
 		return $this->fossilGenus;
	}

	public function getFossilSpecies(): ?string
	{
 		return $this->fossilSpecies;
	}

	public function getFindingPlace(): ?string
	{
 		return $this->findingPlace;
	}

	public function getFindingLayer(): ?string
	{
 		return $this->findingLayer;
	}

	public function getEarthAge(): ?string
	{
 		return $this->earthAge;
	}

	public function getDescriptionAndNotes(): ?string
	{
 		return $this->descriptionAndNotes;
	}


    public function setFindingDate(?string $findingDate): void
	{
		$this->findingDate = $findingDate;
	}

	public function setFossilNumber(?string $fossilNumber): void
	{
		$this->fossilNumber = $fossilNumber;
	}

	public function setFossilGenus(?string $fossilGenus): void
	{
		$this->fossilGenus = $fossilGenus;
	}

	public function setFossilSpecies(?string $fossilSpecies): void
	{
		$this->fossilSpecies = $fossilSpecies;
	}

	public function setFindingPlace(?string $findingPlace): void
	{
		$this->findingPlace = $findingPlace;
	}

	public function setFindingLayer(?string $findingLayer): void
	{
		$this->findingLayer = $findingLayer;
	}

	public function setEarthAge(?string $earthAge): void
	{
		$this->earthAge = $earthAge;
	}

	public function setDescriptionAndNotes(?string $descriptionAndNotes): void
	{
		$this->descriptionAndNotes = $descriptionAndNotes;
	}


}