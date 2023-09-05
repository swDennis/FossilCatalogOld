<?php

namespace App\Tests\FossilForm;

use App\Entity\FossilEntity;
use App\Services\FossilForm\FossilFormEntityCreator;
use App\Tests\Traits\FossilFormFieldRepositoryInterfaceMockTrait;
use PHPUnit\Framework\TestCase;

class FossilFormEntityCreatorTest extends TestCase
{
    use FossilFormFieldRepositoryInterfaceMockTrait;

    private const FINDING_DATE = '2023-06-28';
    private const FOSSIL_NUMBER = '000001';
    private const FOSSIL_NAME = 'Schloenbachia varians';
    private const FINDIG_PLACE = 'Kalkwerke Otto Breckweg in Rheine';
    private const FINDING_LAYER = 'rhotomagense-Zone';
    private const EARTH_AGE = 'Kreide Oberkreide Cenoman';
    private const DESCRIPTION_AND_NOTES = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.';

    public function testCreateFossilFormEntity(): void
    {
        $fossilFormEntityCreator = $this->getFossilFormEntityCreator();

        file_put_contents(FossilFormEntityCreator::FOSSIL_ENTITY_FILE, '');

        // Make sure file is empty
        static::assertEmpty(file_get_contents($fossilFormEntityCreator::FOSSIL_ENTITY_FILE));

        // Test case: Create new file content
        $fossilFormEntityCreator->createFossilFormEntity();

        // Instance entity
        $fossilEntity = new FossilEntity();

        // Test setter
        $fossilEntity->setFindingDate(self::FINDING_DATE);
        $fossilEntity->setFossilNumber(self::FOSSIL_NUMBER);
        $fossilEntity->setFossilName(self::FOSSIL_NAME);
        $fossilEntity->setFindingPlace(self::FINDIG_PLACE);
        $fossilEntity->setFindingLayer(self::FINDING_LAYER);
        $fossilEntity->setEarthAge(self::EARTH_AGE);
        $fossilEntity->setDescriptionAndNotes(self::DESCRIPTION_AND_NOTES);

        // Test getter
        static::assertSame(self::FINDING_DATE, $fossilEntity->getFindingDate());
        static::assertSame(self::FOSSIL_NUMBER, $fossilEntity->getFossilNumber());
        static::assertSame(self::FOSSIL_NAME, $fossilEntity->getFossilName());
        static::assertSame(self::FINDIG_PLACE, $fossilEntity->getFindingPlace());
        static::assertSame(self::FINDING_LAYER, $fossilEntity->getFindingLayer());
        static::assertSame(self::EARTH_AGE, $fossilEntity->getEarthAge());
        static::assertSame(self::DESCRIPTION_AND_NOTES, $fossilEntity->getDescriptionAndNotes());

        // Test fromArray method which is extended from App\Entity\AbstractStruct
        $fossilEntity->fromArray([
            'findingDate' => 'foo',
            'fossilNumber' => 'foo',
            'fossilName' => 'foo',
            'findingPlace' => 'foo',
            'findingLayer' => 'foo',
            'earthAge' => 'foo',
            'descriptionAndNotes' => 'foo',
        ]);

        static::assertSame('foo', $fossilEntity->getFindingDate());
        static::assertSame('foo', $fossilEntity->getFossilNumber());
        static::assertSame('foo', $fossilEntity->getFossilName());
        static::assertSame('foo', $fossilEntity->getFindingPlace());
        static::assertSame('foo', $fossilEntity->getFindingLayer());
        static::assertSame('foo', $fossilEntity->getEarthAge());
        static::assertSame('foo', $fossilEntity->getDescriptionAndNotes());
    }

    public function getFossilFormEntityCreator(): FossilFormEntityCreator
    {
        return new FossilFormEntityCreator($this->createFossilFormFieldRepositoryInterfaceMock());
    }
}