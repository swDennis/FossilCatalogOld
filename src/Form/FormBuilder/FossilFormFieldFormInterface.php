<?php

namespace App\Form\FormBuilder;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

interface FossilFormFieldFormInterface
{
    public function createForm(FormBuilderInterface $builder, string $actionUrl): FormInterface;
}