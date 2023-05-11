<?php

namespace App\Services\FossilForm;

use App\Exceptions\CreateColumException;
use App\Exceptions\CreateFulltextIndexException;
use App\Exceptions\DeleteFulltextIndexException;
use App\Form\FormBuilder\FormFieldType;
use App\Repository\FossilFormFieldRepositoryInterface;
use Doctrine\DBAL\Connection;

class FossilFormEntityDatabaseCreator
{
    public const FOSSIL_DATABASE_TABLE_NAME = 'fossil_entity';

    private const MATCH_AGAINST_SEARCH_INDEX = 'IDX_match_against_search_index';

    public function __construct(
        private readonly FossilFormFieldRepositoryInterface $fossilFormFieldRepository,
        private readonly Connection $connection
    ) {}

    public function addDatabaseColumns(): void
    {
        foreach ($this->fossilFormFieldRepository->getFossilFormFieldList() as $formField) {
            if ($this->checkIfColumnExist(self::FOSSIL_DATABASE_TABLE_NAME, $formField[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_NAME])) {
                continue;
            }

            $this->createDatabaseField(
                $formField[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_NAME],
                $formField[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_TYPE]
            );

            if (!$this->checkIfColumnExist(self::FOSSIL_DATABASE_TABLE_NAME, $formField[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_NAME])) {
                throw new CreateColumException(
                    $formField[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_NAME],
                    self::FOSSIL_DATABASE_TABLE_NAME
                );
            }
        }

        $this->dropFulltextIndex();
        $this->createFulltextIndex();
    }

    private function dropFulltextIndex(): void
    {
        $sqlTemplate = 'DROP INDEX %s ON %s;';

        $sql = sprintf($sqlTemplate, self::MATCH_AGAINST_SEARCH_INDEX, self::FOSSIL_DATABASE_TABLE_NAME);
        try {
            $this->connection->executeQuery($sql);
        } catch (\Exception $exception) {
            throw new DeleteFulltextIndexException(
                self::MATCH_AGAINST_SEARCH_INDEX,
                self::FOSSIL_DATABASE_TABLE_NAME,
                $exception
            );
        }
    }

    private function createFulltextIndex()
    {
        $sqlTemplate ='ALTER TABLE %s ADD FULLTEXT %s(%s);';
        $filterableFields = $this->fossilFormFieldRepository->getFilterableFields();
        $filterableFieldNames = array_column($filterableFields, FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_NAME);
        $sql = sprintf($sqlTemplate, self::FOSSIL_DATABASE_TABLE_NAME, self::MATCH_AGAINST_SEARCH_INDEX, implode(',', $filterableFieldNames));

        try {
            $this->connection->executeQuery($sql);
        } catch (\Exception $exception) {
            throw new CreateFulltextIndexException(
                self::MATCH_AGAINST_SEARCH_INDEX,
                self::FOSSIL_DATABASE_TABLE_NAME,
                $exception
            );
        }
    }

    private function createDatabaseField(string $columName, string $type): void
    {
        $sql = $this->createSql($columName, $type);

        try {
            $this->connection->executeQuery($sql);
        } catch (\Exception $exception) {
            throw new CreateColumException(
                $columName,
                self::FOSSIL_DATABASE_TABLE_NAME,
                $exception
            );
        }
    }

    private function createSql(string $columName, string $type)
    {
        $sql = $this->createAddColumnStatement($columName, $type);

        if (!$this->checkColumnRequiresIndex($type)) {
            return $sql;
        }

        $template = '
            %s
            %s
        ';

        return sprintf($template, $sql, $this->createAddIndexStatement($columName, $type));
    }

    private function createAddColumnStatement(string $columName, string $type): string
    {
        $sqlAlterTableTemplate = 'ALTER TABLE `%s` ADD COLUMN `%s` %s NULL DEFAULT NULL;';

        return sprintf(
            $sqlAlterTableTemplate,
            self::FOSSIL_DATABASE_TABLE_NAME,
            $columName,
            $this->mapType($type)
        );
    }

    private function createAddIndexStatement(string $columName, string $type)
    {
        $sqlAddIndexTemplate = 'ALTER TABLE %s ADD INDEX %s (%s);';

        return sprintf(
            $sqlAddIndexTemplate,
            self::FOSSIL_DATABASE_TABLE_NAME,
            sprintf('%s_%s_%s_INDEX', self::FOSSIL_DATABASE_TABLE_NAME, $columName, $type),
            $columName
        );
    }

    private function checkColumnRequiresIndex(string $type): bool
    {
        return $type === FormFieldType::TEXT || $type === FormFieldType::NUMBER;
    }

    private function mapType(string $type): string
    {
        switch ($type) {
            case FormFieldType::TEXT_AREA:
                return 'TEXT';
            case FormFieldType::DATE;
                return 'VARCHAR(50)';
            default:
                return 'VARCHAR(255)';
        }
    }

    private function checkIfColumnExist($tableName, $columnName): bool
    {
        $sql = sprintf('SHOW COLUMNS FROM `%s` LIKE ?', $tableName);

        $columnNameInDb = $this->connection->executeQuery(
            $sql,
            [$columnName]
        )->fetchOne();

        return $columnNameInDb === $columnName;
    }
}