<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateUserForm extends AbstractType implements CreateUserFormInterface
{
    public function createForm(FormBuilderInterface $builder, string $actionUrl): FormInterface
    {
        $builder->add('createUser', SubmitType::class, [
            'label' => 'Benutzer anlegen',
            'attr' => ['class' => 'btn btn-primary'],
        ]);

        $builder->setAction($actionUrl);
        $builder->setMethod('POST');

        $builder->add('email', EmailType::class, [
            'constraints' => [
                new NotBlank(),
            ],
            'label' => 'Your E-Mail',
        ]);

        $builder->add('password', PasswordType::class, [
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 8, 'max' => 4000]),
            ],
            'label' => 'Your password',
        ]);

        $builder->add('passwordConfirm', PasswordType::class, [
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 8, 'max' => 4000]),
            ],
            'label' => 'Confirm your password',
        ]);

        return $builder->getForm();
    }
}