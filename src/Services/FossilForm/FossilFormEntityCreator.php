<?php

namespace App\Services\FossilForm;

use App\Entity\FossilFormField;
use App\Repository\FossilFormFieldRepositoryInterface;

class FossilFormEntityCreator
{
    public const FOSSIL_ENTITY_NAME = 'FossilEntity';

    public const FOSSIL_ENTITY_FILE = __DIR__ . '/../../Entity/FossilEntity.php';

    private FossilFormFieldRepositoryInterface $fossilFormFieldRepository;

    public function __construct(FossilFormFieldRepositoryInterface $fossilFormFieldRepository)
    {
        $this->fossilFormFieldRepository = $fossilFormFieldRepository;
    }

    public function createFossilFormEntity(): void
    {
        $formFields = $this->fossilFormFieldRepository->getFossilFormFieldList();

        $properties = $this->createPropertiesString($formFields);

        $getter = $this->createGetterString($formFields);

        $setter = $this->createSetterString($formFields);

        $template = $this->getTemplate();

        $renderedEntityCode = sprintf($template, self::FOSSIL_ENTITY_NAME, $properties, $getter, $setter);

        file_put_contents(self::FOSSIL_ENTITY_FILE, $renderedEntityCode, LOCK_EX);
    }

    /**
     * @param array<FossilFormField> $formFields
     */
    private function createSetterString(array $formFields): string
    {
        $template = "%spublic function set%s(?string $%s): void%s\t{%s\t\t\$this->%s = $%s;%s\t}" . PHP_EOL . PHP_EOL;

        $result = '';
        $first = true;
        foreach ($formFields as $formField) {
            $result .= sprintf(
                $template,
                $first ? '' : "\t",
                ucfirst($formField->getFieldName()),
                $formField->getFieldName(),
                PHP_EOL,
                PHP_EOL,
                $formField->getFieldName(),
                $formField->getFieldName(),
                PHP_EOL
            );

            $first = false;
        }

        return $result;
    }

    /**
     * @param array<FossilFormField> $formFields
     */
    private function createGetterString(array $formFields): string
    {
        $template = "%spublic function get%s(): ?string%s\t{%s \t\treturn \$this->%s;%s\t}" . PHP_EOL . PHP_EOL;
        $result = '';
        $first = true;
        foreach ($formFields as $formField) {
            $result .= sprintf(
                $template,
                $first ? '' : "\t",
                ucfirst($formField->getFieldName()),
                PHP_EOL,
                PHP_EOL,
                $formField->getFieldName(),
                PHP_EOL
            );

            $first = false;
        }

        return $result;
    }

    /**
     * @param array<FossilFormField> $formFields
     */
    private function createPropertiesString(array $formFields): string
    {
        $template = "%sprotected ?string $%s = null;" . PHP_EOL . PHP_EOL;
        $result = '';
        $first = true;
        foreach ($formFields as $formField) {
            $result .= sprintf(
                $template,
                $first ? '' : "\t",
                $formField->getFieldName()
            );

            $first = false;
        }

        return $result;
    }

    private function getTemplate(): string
    {
        return '<?php

namespace App\Entity;

class %s extends AbstractStruct 
{
    protected ?int $id = null;
    
    protected bool $showInOverview = false;
    
    protected array $images = [];
    
    protected array $categories = [];
    
    protected array $tags = [];
    
    %s

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }
    
    public function getShowInOverview(): bool
    {
        return $this->showInOverview;
    }

    public function setShowInOverview(bool $showInOverview): void
    {
        $this->showInOverview = $showInOverview;
    }
    
    public function getImages(): array
    {
        return $this->images;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }
    
    public function getTags(): array
    {
        return $this->tags;
    }

    public function setImages(array $images): void
    {
        $this->images = $images;
    }
    
    public function addImage(Image $image): void
    {
        $this->images[] = $image;
    }
    
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }
    
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }
    
    %s
    %s
}';
    }
}