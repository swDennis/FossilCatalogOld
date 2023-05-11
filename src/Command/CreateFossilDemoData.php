<?php

namespace App\Command;

use App\Entity\Fossil;
use App\Entity\FossilEntity;
use App\Entity\Tag;
use App\Form\FormBuilder\FormFieldType;
use App\Repository\FossilFormFieldRepositoryInterface;
use App\Repository\FossilRepositoryInterface;
use App\Repository\TagRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:create-fossil-demo-data',
    description: 'Creates a set of fossil demo data',
    hidden: false,
    aliases: ['app:create-fossil-demo-data']
)]
class CreateFossilDemoData extends Command
{
    protected static $defaultName = 'app:create-fossil-demo-data';

    protected static $defaultDescription = 'Creates a set of fossil demo data';

    private array $numbers = [];

    public function __construct(
        private readonly FossilRepositoryInterface $fossilRepository,
        private readonly FossilFormFieldRepositoryInterface $fossilFormFieldRepository,
        private readonly TagRepositoryInterface $tagRepository
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createTagsAndCategories($output);

        for ($i = 0; $i < 10000; $i++) {
            $this->fossilRepository->saveFossil($this->createFossilEntity($i));

            if ($i % 100 === 0) {
                $output->write($i . ' Fossils created');
                $output->write(PHP_EOL);
            }
        }

        return Command::SUCCESS;
    }

    private function createTagsAndCategories(OutputInterface $output)
    {
        for ($i = 0; $i < 50; $i++) {
            $tag = new Tag();
            $tag->setName($this->getContent(FormFieldType::TEXT));
            $tag->setIsUsedAsCategory(1);
            $this->tagRepository->saveTag($tag);
        }

        $output->write('50 Categories created');
        $output->write(PHP_EOL);

        for ($i = 0; $i < 50; $i++) {
            $tag = new Tag();
            $tag->setName($this->getContent(FormFieldType::TEXT));
            $tag->setIsUsedAsCategory(0);
            $this->tagRepository->saveTag($tag);
        }

        $output->write('50 Tags created');
        $output->write(PHP_EOL);
    }

    private function createFossilEntity($number): FossilEntity
    {
        $fossil = new FossilEntity();

        $fields = $this->fossilFormFieldRepository->getFossilFormFieldList();

        foreach ($fields as $field) {
            $setter = sprintf('set%s', ucfirst($field[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_NAME]));
            if ($setter === 'setFossilNumber') {
                $fossil->setFossilNumber($number);
                continue;
            }

            $fossil->$setter($this->getContent($field[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_TYPE]));
        }

        $fossil->setCategories([rand(1, 50)]);
        $fossil->setTags($this->createTagIds());

        return $fossil;
    }

    private function createTagIds()
    {
        $counter = 0;
        $tagIds = [];
        while ($counter < 2) {
            $newId = rand(51, 100);
            if (in_array($newId, $tagIds, true)) {
                continue;
            }

            $tagIds[] = $newId;
            $counter++;
        }

        return $tagIds;
    }

    private function getContent(string $type)
    {
        switch ($type) {
            case FormFieldType::TEXT_AREA:
                return $this->getLongText();
            case FormFieldType::DATE:
                return (new \DateTime('01.01.1970'))->format('Y-m-d');
            case FormFieldType::NUMBER:
                return rand(10000, 99999);
            default:
                return $this->getRandom($this->getTextArray()) .  rand(0, 99999);
        }
    }

    private function getRandom(array $list): string
    {
        return $list[array_rand($list, 1)];
    }

    private function getLongText()
    {
        return 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.';
    }

    private function getTextArray()
    {
        return [
            'consetetur',
            'sadipscing',
            'tempor',
            'invidunt',
            'magna',
            'takimata',
            'gubergren',
            'accusam',
            'eos',
            'ipsum',
            'gubergren',
        ];
    }
}