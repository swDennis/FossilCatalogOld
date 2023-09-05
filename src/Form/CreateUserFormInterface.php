<?php

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

interface CreateUserFormInterface
{
    public function createForm(FormBuilderInterface $builder, string $actionUrl): FormInterface;
}