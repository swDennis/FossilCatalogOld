<?php

namespace App\Form;

use App\Validator\TagName\TagInfo;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

interface TagFormInterface
{
    public function createForm(FormBuilderInterface $builder, string $actionUrl, TagInfo $tagInfo): FormInterface;
}