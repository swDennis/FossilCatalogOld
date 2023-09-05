<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class ImportForm extends AbstractType implements ImportFormInterface
{
    public function createForm(FormBuilderInterface $builder, string $actionUrl): FormInterface
    {
        $builder->add('upload', SubmitType::class, [
            'label' => 'Hochladen',
            'attr' => ['class' => 'btn btn-primary'],
        ]);

        $builder->add('import', FileType::class, [
            'label' => 'Datensicherung hochladen',
            'attr' => [
                'accept' => 'zip',
                'class' => 'form-control',
            ],
        ]);

        $builder->setAction($actionUrl);
        $builder->setMethod('POST');

        return $builder->getForm();
    }
}