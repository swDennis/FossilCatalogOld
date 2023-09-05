<?php

namespace App\Services\Filter;

use App\Repository\FossilFormFieldRepositoryInterface;
use App\Repository\TagRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestFilterFactory implements RequestFilterFactoryInterface
{
    public function __construct(
        private readonly FossilFormFieldRepositoryInterface $fossilFormFieldRepository,
        private readonly TagRepositoryInterface $tagRepository
    ) {

    }

    public function addFilterFromRequest(Request $request): array
    {
        $selectedCategories = $request->get('categories');
        $selectedTags = $request->get('tags');

        $filterArray = [
            'availableCategories' => $this->tagRepository->getTagsThatAreAssignedToFossils(TagRepositoryInterface::GET_ONLY_CATEGORIES, $selectedTags),
            'selectedCategories' => $selectedCategories,
            'categories' => $selectedCategories,
            'availableTags' => $this->tagRepository->getTagsThatAreAssignedToFossils(TagRepositoryInterface::GET_ONLY_TAGS, $selectedCategories),
            'selectedTags' => $selectedTags,
            'tags' => $selectedTags,
            'searchTerm' => $request->get('searchTerm', null),
        ];

        return $filterArray;
    }
}