<?php

namespace App\Services\Filter;

use Symfony\Component\HttpFoundation\Request;

interface RequestFilterFactoryInterface
{
    public function addFilterFromRequest(Request $request): array;
}