<?php

namespace App\Validator\TagName;

use App\Repository\TagRepositoryInterface;

class TagInfo
{
    private ?array $tag;

    public function __construct(
        private readonly TagRepositoryInterface $tagRepository,
        private readonly ?int $requestUrlTagId,
        private readonly ?array $postedTag,
    ) {
        if ($this->requestUrlTagId) {
            $this->tag = $this->tagRepository->getById($this->requestUrlTagId);
        } elseif (array_key_exists('id', $this->postedTag) && !empty($this->postedTag['id'])) {
            $this->tag = $this->tagRepository->getById($this->postedTag['id']);
        }
    }

    public function isValidationRequired(): bool
    {
        if ($this->requestUrlTagId !== null && empty($this->postedTag)) {
            return false;
        }

        if (!empty($this->postedTag) && !empty($this->tag)) {
            if (strtolower(trim($this->tag['name'])) === strtolower(trim($this->postedTag['name']))) {
                return false;
            }
        }

        return true;
    }
}