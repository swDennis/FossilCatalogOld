<?php

namespace App\Entity;

class Image extends AbstractStruct
{
    protected ?int $id = null;

    protected ?int $fossilId = null;

    protected ?string $imageName = null;

    protected ?string $thumbnailName = null;

    protected ?string $mimeType = null;

    protected ?string $relativePath = null;

    protected ?string $relativeImagePath = null;

    protected ?string $relativeThumbnailPath = null;

    protected ?string $absolutePath = null;

    protected ?string $absoluteImagePath = null;

    protected ?string $absoluteThumbnailPath = null;

    protected bool $showInGallery = false;

    protected bool $isMainImage = false;

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
     * @return int|null
     */
    public function getFossilId(): ?int
    {
        return $this->fossilId;
    }

    /**
     * @param int|null $fossilId
     */
    public function setFossilId(?int $fossilId): void
    {
        $this->fossilId = $fossilId;
    }

    /**
     * @return string|null
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * @param string|null $imageName
     */
    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    /**
     * @return string|null
     */
    public function getThumbnailName(): ?string
    {
        return $this->thumbnailName;
    }

    /**
     * @param string|null $thumbnailName
     */
    public function setThumbnailName(?string $thumbnailName): void
    {
        $this->thumbnailName = $thumbnailName;
    }

    /**
     * @return string|null
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * @param string|null $mimeType
     */
    public function setMimeType(?string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    /**
     * @return string|null
     */
    public function getRelativePath(): ?string
    {
        return $this->relativePath;
    }

    /**
     * @param string|null $relativePath
     */
    public function setRelativePath(?string $relativePath): void
    {
        $this->relativePath = $relativePath;
    }

    /**
     * @return string|null
     */
    public function getRelativeImagePath(): ?string
    {
        return $this->relativeImagePath;
    }

    /**
     * @param string|null $relativeImagePath
     */
    public function setRelativeImagePath(?string $relativeImagePath): void
    {
        $this->relativeImagePath = $relativeImagePath;
    }

    /**
     * @return string|null
     */
    public function getRelativeThumbnailPath(): ?string
    {
        return $this->relativeThumbnailPath;
    }

    /**
     * @param string|null $relativeThumbnailPath
     */
    public function setRelativeThumbnailPath(?string $relativeThumbnailPath): void
    {
        $this->relativeThumbnailPath = $relativeThumbnailPath;
    }

    /**
     * @return string|null
     */
    public function getAbsolutePath(): ?string
    {
        return $this->absolutePath;
    }

    /**
     * @param string|null $absolutePath
     */
    public function setAbsolutePath(?string $absolutePath): void
    {
        $this->absolutePath = $absolutePath;
    }

    /**
     * @return string|null
     */
    public function getAbsoluteImagePath(): ?string
    {
        return $this->absoluteImagePath;
    }

    /**
     * @param string|null $absoluteImagePath
     */
    public function setAbsoluteImagePath(?string $absoluteImagePath): void
    {
        $this->absoluteImagePath = $absoluteImagePath;
    }

    /**
     * @return string|null
     */
    public function getAbsoluteThumbnailPath(): ?string
    {
        return $this->absoluteThumbnailPath;
    }

    /**
     * @param string|null $absoluteThumbnailPath
     */
    public function setAbsoluteThumbnailPath(?string $absoluteThumbnailPath): void
    {
        $this->absoluteThumbnailPath = $absoluteThumbnailPath;
    }

    /**
     * @return bool
     */
    public function getShowInGallery(): bool
    {
        return $this->showInGallery;
    }

    /**
     * @param bool $showInGallery
     */
    public function setShowInGallery(bool $showInGallery): void
    {
        $this->showInGallery = $showInGallery;
    }

    /**
     * @return bool
     */
    public function getIsMainImage(): bool
    {
        return $this->isMainImage;
    }

    /**
     * @param bool $isMainImage
     */
    public function setIsMainImage(bool $isMainImage): void
    {
        $this->isMainImage = $isMainImage;
    }
}