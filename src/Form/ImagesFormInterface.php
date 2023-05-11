<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

interface ImagesFormInterface
{
    public function createForm(FormBuilderInterface $builder, string $actionUrl): FormInterface;
}