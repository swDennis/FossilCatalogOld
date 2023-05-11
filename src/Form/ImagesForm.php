<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class ImagesForm extends AbstractType implements ImagesFormInterface
{
    public function createForm(FormBuilderInterface $builder, string $actionUrl): FormInterface
    {
        $builder->add('upload', SubmitType::class, [
            'label' => 'Hochladen',
            'attr' => ['class' => 'btn btn-primary'],
        ]);

        $builder->add('fossilId', HiddenType::class);

        $builder->add('images', FileType::class, [
            'label' => 'Bilder hochladen',
            'multiple' => true,
            'attr' => [
                'accept' => 'image/*',
                'multiple' => 'multiple',
                'class' => 'form-control',
            ],
        ]);

        $builder->setAction($actionUrl);
        $builder->setMethod('POST');

        return $builder->getForm();
    }
}