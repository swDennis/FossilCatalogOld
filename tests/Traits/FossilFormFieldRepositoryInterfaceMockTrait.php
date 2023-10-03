<?php
//
//namespace App\Tests\Traits;
//
//use App\Repository\FossilFormFieldRepositoryInterface;
//
//trait FossilFormFieldRepositoryInterfaceMockTrait
//{
//    public function createFossilFormFieldRepositoryInterfaceMock(): FossilFormFieldRepositoryInterface
//    {
//        $fossilFormFieldRepositoryInterfaceMock = $this->createMock(FossilFormFieldRepositoryInterface::class);
//        $fossilFormFieldRepositoryInterfaceMock->method('getFossilFormFieldList')->willReturn($this->getGetFossilFormFieldListResult());
//
//        return $fossilFormFieldRepositoryInterfaceMock;
//    }
//
//    private function getGetFossilFormFieldListResult(): array
//    {
//        return [
//            [
//                'id' => 1,
//                'fieldOrder' => 0,
//                'fieldName' => 'findingDate',
//                'fieldLabel' => 'Funddatum',
//                'fieldType' => 'date',
//            ], [
//                'id' => 2,
//                'fieldOrder' => 1,
//                'fieldName' => 'fossilNumber',
//                'fieldLabel' => 'Nummer',
//                'fieldType' => 'number',
//            ], [
//                'id' => 3,
//                'fieldOrder' => 2,
//                'fieldName' => 'fossilName',
//                'fieldLabel' => 'Name',
//                'fieldType' => 'text',
//            ], [
//                'id' => 4,
//                'fieldOrder' => 3,
//                'fieldName' => 'findingPlace',
//                'fieldLabel' => 'Fundort',
//                'fieldType' => 'text',
//            ], [
//                'id' => 5,
//                'fieldOrder' => 4,
//                'fieldName' => 'findingLayer',
//                'fieldLabel' => 'Fundschicht',
//                'fieldType' => 'text',
//            ], [
//                'id' => 6,
//                'fieldOrder' => 5,
//                'fieldName' => 'earthAge',
//                'fieldLabel' => 'Erdzeitalter',
//                'fieldType' => 'text',
//            ], [
//                'id' => 7,
//                'fieldOrder' => 6,
//                'fieldName' => 'descriptionAndNotes',
//                'fieldLabel' => 'Beschreibung und Anmerkungen',
//                'fieldType' => 'textarea',
//            ],
//        ];
//    }
//}