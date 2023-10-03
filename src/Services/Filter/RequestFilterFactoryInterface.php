<?php

namespace App\Services\Filter;

use Symfony\Component\HttpFoundation\Request;

interface RequestFilterFactoryInterface
{
    /**
     * @return array<string, mixed>
     */
    public function addFilterFromRequest(Request $request): array;
}