<?php

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

interface FossilFormInterface
{
    public function createForm(FormBuilderInterface $builder, string $actionUrl): FormInterface;
}