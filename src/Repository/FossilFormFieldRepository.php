<?php

namespace App\Repository;

use App\Entity\FossilFormField;
use App\Exceptions\IsNotNumericException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class FossilFormFieldRepository implements FossilFormFieldRepositoryInterface, RepositoryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function saveFossilFormField(FossilFormField $fossilFormField, ?bool $isNew = null): FossilFormField
    {
        if ($isNew === null) {
            $isNew = $fossilFormField->getId() === null;
        }

        $queryBuilder = $this->createInsertUpdateQueryBuilder($fossilFormField, $isNew);

        try {
            $queryBuilder->executeQuery();
        } catch (\Exception $exception) {
            throw new \RuntimeException(sprintf('Could not save FossilFormField entity: %s', $exception->getMessage()));
        }


        if (!$isNew) {
            return $fossilFormField;
        }

        $id = $this->connection->lastInsertId();
        if ($id === false) {
            throw new \RuntimeException('Could not create FossilFormField entity');
        }

        $fossilFormField->setId((int) $id);

        return $fossilFormField;
    }

    public function getFossilFormFieldList(): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(FossilFormFieldRepositoryInterface::FORM_FIELD_TABLE_NAME)
            ->orderBy(FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_ORDER)
            ->executeQuery()
            ->fetchAllAssociative();

        return $this->prepareListResult($result);
    }

    public function getFossilFormFieldListForOverview(): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(FossilFormFieldRepositoryInterface::FORM_FIELD_TABLE_NAME)
            ->where(FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_SHOW_IN_OVERVIEW)
            ->orderBy(FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_ORDER)
            ->executeQuery()
            ->fetchAllAssociative();

        return $this->prepareListResult($result);
    }

    public function getFossilFormFieldById(int $id): ?FossilFormField
    {
        $result = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(FossilFormFieldRepositoryInterface::FORM_FIELD_TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();

        if (!$result) {
            return null;
        }

        return (new FossilFormField())->fromArray($result);
    }

    public function deleteFossilFormField(int $id): void
    {
        $this->connection->createQueryBuilder()
            ->delete(FossilFormFieldRepositoryInterface::FORM_FIELD_TABLE_NAME)
            ->where('id = :id')
            ->andWhere('isRequiredDefault = 0')
            ->setParameter('id', $id)
            ->executeQuery();
    }

    public function getFilterableFields(): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(self::FORM_FIELD_TABLE_NAME)
            ->where(self::FORM_FIELD_COLUMN_FIELD_IS_FILTER . ' = 1')
            ->andWhere('fieldType NOT LIKE "date"')
            ->executeQuery()
            ->fetchAllAssociative();

        return $this->prepareListResult($result);
    }

    public function getNewOrderNumber(): int
    {
        $highestOrderNumber = $this->connection->createQueryBuilder()
            ->select('MAX(' . self::FORM_FIELD_COLUMN_FIELD_ORDER . ')')
            ->from(self::FORM_FIELD_TABLE_NAME)
            ->executeQuery()
            ->fetchOne();

        return ++$highestOrderNumber;
    }

    public function getExportList(int $limit, int $offset): array
    {
        return $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(self::FORM_FIELD_TABLE_NAME)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function getColumnCount(): int
    {
        $result = $this->connection->createQueryBuilder()
            ->select(['COUNT(id)'])
            ->from(self::FORM_FIELD_TABLE_NAME)
            ->executeQuery()
            ->fetchOne();

        if (!is_numeric($result)) {
            throw new IsNotNumericException($this);
        }

        return (int) $result;
    }

    /**
     * @param array<int, array<string, mixed>> $result
     *
     * @return array<FossilFormField>
     */
    private function prepareListResult(array $result): array
    {
        $array = [];
        foreach ($result as $fossilFormField) {
            $array[] = (new FossilFormField())->fromArray($fossilFormField);
        }

        return $array;
    }

    private function createInsertUpdateQueryBuilder(
        FossilFormField $fossilFormField,
        bool            $isNewFormField
    ): QueryBuilder {
        $queryBuilder = $this->connection->createQueryBuilder();
        if ($isNewFormField) {
            $function = self::INSERT_FUNCTION;
            $queryBuilder->insert(self::FORM_FIELD_TABLE_NAME);
        } else {
            $function = self::UPDATE_FUNCTION;
            $queryBuilder->update(self::FORM_FIELD_TABLE_NAME)
                ->where('id = :id')
                ->setParameter('id', $fossilFormField->getId());
        }

        $queryBuilder
            ->$function(self::FORM_FIELD_COLUMN_FIELD_NAME, ':fieldName')
            ->$function(self::FORM_FIELD_COLUMN_FIELD_LABEL, ':fieldLabel')
            ->$function(self::FORM_FIELD_COLUMN_FIELD_TYPE, ':fieldType')
            ->$function(self::FORM_FIELD_COLUMN_FIELD_ORDER, ':fieldOrder')
            ->$function(self::FORM_FIELD_COLUMN_FIELD_SHOW_IN_OVERVIEW, ':showInOverview')
            ->$function(self::FORM_FIELD_COLUMN_FIELD_ALLOW_BLANK, ':allowBlank')
            ->$function(self::FORM_FIELD_COLUMN_FIELD_IS_FILTER, ':isFilter')
            ->$function(self::FORM_FIELD_COLUMN_IS_REQUIRED_DEFAULT, ':isRequiredDefault')
            ->setParameter('fieldName', $fossilFormField->getFieldName())
            ->setParameter('fieldLabel', $fossilFormField->getFieldLabel())
            ->setParameter('fieldType', $fossilFormField->getFieldType())
            ->setParameter('fieldOrder', $fossilFormField->getFieldOrder())
            ->setParameter('showInOverview', (int) $fossilFormField->getShowInOverview())
            ->setParameter('allowBlank', (int) $fossilFormField->getAllowBlank())
            ->setParameter('isFilter', (int) $fossilFormField->getIsFilter())
            ->setParameter('isRequiredDefault', (int) $fossilFormField->getIsRequiredDefault());

        return $queryBuilder;
    }

//    public function getFilterableFieldsPopulatedWithValues(): array
//    {
//        $filterAbleFields = $this->getFilterableFields();
//
//        foreach ($filterAbleFields as &$filterAbleField) {
//            $filterAbleField->setValue($this->getValuesForFilterableField($filterAbleField['fieldName']));
//        }
//
//        return $filterAbleFields;
//    }

//    private function getValuesForFilterableField(string $fieldName): array
//    {
//        return $this->connection->createQueryBuilder()
//            ->select('DISTINCT ' . $fieldName)
//            ->from(FossilRepositoryInterface::FOSSIL_TABLE_NAME)
//            ->executeQuery()
//            ->fetchFirstColumn();
//    }
}