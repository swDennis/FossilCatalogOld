<?php

namespace App\Form;

use App\Validator\TagName\TagInfo;
use App\Validator\TagName\TagNameConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TagForm extends AbstractType implements TagFormInterface
{
    public function createForm(FormBuilderInterface $builder, string $actionUrl, TagInfo $tagInfo): FormInterface
    {
        $builder->add('createTag', SubmitType::class, [
            'label' => 'Speichern',
            'attr' => ['class' => 'btn btn-primary'],
        ]);

        $builder->setAction($actionUrl);
        $builder->setMethod('POST');

        $builder->add('id');

        $builder->add('name', TextType::class, [
            'label' => 'Kategorie oder Tag Name',
            'constraints' => [
                new NotBlank(),
                new TagNameConstraint($tagInfo),
            ],
        ]);

        $builder->add('isUsedAsCategory', ChoiceType::class, [
            'label' => 'Als Kategorie verwenden',
            'choices' => [
                'Ja' => 1,
                'Nein' => 0,
            ],
        ]);

        return $builder->getForm();
    }
}
