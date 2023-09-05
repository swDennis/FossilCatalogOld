<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class InstallationForm extends AbstractType implements InstallationFormInterface
{
    public function createForm(FormBuilderInterface $builder, string $actionUrl): FormInterface
    {
        $builder->add('install', SubmitType::class, [
            'label' => 'Erstelle Datenbank',
            'attr' => ['class' => 'btn btn-primary'],
        ]);

        $builder->setAction($actionUrl);
        $builder->setMethod('POST');

        $builder->add('databaseName', TextType::class, [
            'constraints' => [
                new NotBlank(),
            ],
            'label' => 'Database name',
        ]);

        $builder->add('databaseUsername', TextType::class, [
            'constraints' => [
                new NotBlank(),
            ],
            'label' => 'Database user',
        ]);

        $builder->add('databasePassword', PasswordType::class, [
            'constraints' => [
                new NotBlank(),
            ],
            'label' => 'Database password',
        ]);

        $builder->add('databaseHost', TextType::class, [
            'constraints' => [
                new NotBlank(),
            ],
            'label' => 'Database host',
        ]);

        $builder->add('databasePort', TextType::class, [
            'constraints' => [
                new NotBlank(),
            ],
            'label' => 'Database port',
        ]);

        return $builder->getForm();
    }
}