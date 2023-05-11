<?php

namespace App\Form;

use App\Entity\Tag;
use App\Form\FormBuilder\FormFieldType;
use App\Form\Transformer\TagTransformer;
use App\Repository\FossilFormFieldRepositoryInterface;
use App\Repository\TagRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FossilForm extends AbstractType implements FossilFormInterface
{
    public function __construct(
        private readonly FossilFormFieldRepositoryInterface $fossilFormFieldRepository,
        private readonly TagRepositoryInterface $tagRepository
    ) {
    }

    public function createForm(FormBuilderInterface $builder, string $actionUrl): FormInterface
    {
        $formFields = $this->fossilFormFieldRepository->getFossilFormFieldList();

        $builder->setAction($actionUrl);
        $builder->setMethod('POST');

        $builder->add('fossil', SubmitType::class, [
            'label' => 'Speichern',
            'attr' => ['class' => 'btn btn-primary'],
        ]);

        $builder->add('id');

        $builder->add('images', FileType::class, [
            'label' => 'Bilder',
            'multiple' => true,
            'required' => false,
            'attr' => [
                'accept' => 'image/*',
                'multiple' => 'multiple',
                'class' => 'form-control',
            ],
        ]);

        $builder->add('categories', ChoiceType::class, [
            'label' => 'Kategorien',
            'choices' => $this->getCategoryChoiceList(),
            'multiple' => true,
            'attr' => [
                'multiple' => 'multiple',
                'class' => 'form-control',
            ],
        ]);

        $builder->get('categories')
            ->addModelTransformer(new TagTransformer());

        $builder->add('tags', ChoiceType::class, [
            'label' => 'Tags',
            'choices' => $this->getTagChoiceList(),
            'multiple' => true,
            'attr' => [
                'multiple' => 'multiple',
                'class' => 'form-control',
            ],
        ]);

        $builder->get('tags')
            ->addModelTransformer(new TagTransformer());

        foreach ($formFields as $formField) {
            $builder->add(
                $formField[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_NAME],
                $this->getFieldTypeClass($formField[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_TYPE]),
                [
                    'label' => $formField[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_LABEL],
                ]
            );
        }

        return $builder->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }

    private function getFieldTypeClass(string $type)
    {
        switch ($type) {
            case FormFieldType::TEXT_AREA:
                return TextareaType::class;
            default:
                return TextType::class;
        }
    }

    /**
     * @return array
     */
    public function getCategoryChoiceList(): array
    {
        $categories = $this->tagRepository->getList(TagRepositoryInterface::GET_ONLY_CATEGORIES);

        return $this->getChoiceList($categories);
    }

    /**
     * @return array
     */
    public function getTagChoiceList(): array
    {
        $tags = $this->tagRepository->getList(TagRepositoryInterface::GET_ONLY_TAGS);

        return $this->getChoiceList($tags);
    }

    private function getChoiceList(array $list)
    {
        $choiceList = [];
        foreach ($list as $listItem) {
            $choiceList[$listItem['name']] = $listItem['id'];
        }

        return $choiceList;
    }
}
