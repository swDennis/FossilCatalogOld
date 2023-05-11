<?php

namespace App\Form\FormBuilder;

use App\Validator\MysqlKeyword\MysqlKeywordConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class FossilFormFieldForm extends AbstractType implements FossilFormFieldFormInterface
{
    public function createForm(FormBuilderInterface $builder, string $actionUrl): FormInterface
    {
        $builder->setAction($actionUrl);
        $builder->setMethod('POST');

        $builder->add('fossilFormField', SubmitType::class, [
            'label' => 'Speichern',
            'attr' => ['class' => 'btn btn-primary'],
        ]);

        $builder->add('id', HiddenType::class);

        $builder->add('fieldName', TextType::class, [
            'label' => 'Eingabefeld Name',
            'constraints' => [
                new NotBlank(),
                new MysqlKeywordConstraint(),
            ],
        ]);

        $builder->add('fieldLabel', TextType::class, [
            'label' => 'Eingabefeld Anzeigename',
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        $builder->add('fieldType', ChoiceType::class, [
            'label' => 'Typ',
            'choices' => [
                'Textfeld' => FormFieldType::TEXT,
                'Textbereich' => FormFieldType::TEXT_AREA,
                'Nummer' => FormFieldType::NUMBER,
                'Datum' => FormFieldType::DATE,
            ],
        ]);

        $builder->add('fieldOrder', NumberType::class, [
            'label' => 'Reihenfolge',
        ]);

        $builder->add('showInOverview', ChoiceType::class, [
            'label' => 'In Übersicht anzeigen',
            'choices' => [
                'Ja' => 1,
                'Nein' => 0,
            ],
        ]);

        $builder->add('allowBlank', ChoiceType::class, [
            'label' => 'Feld kann leer bleiben  ',
            'choices' => [
                'Ja' => 1,
                'Nein' => 0,
            ],
        ]);

        $builder->add('isFilter', ChoiceType::class, [
            'label' => 'Als Filter für die Suche verwenden',
            'choices' => [
                'Ja' => 1,
                'Nein' => 0,
            ],
        ]);

        $builder->add('fieldOrder', NumberType::class, [
            'label' => 'Reihenfolge',
        ]);

        return $builder->getForm();
    }
}