<?php

namespace App\Validator\MysqlKeyword;

use App\Services\FossilForm\MysqlKeyWordFilterInterface;
use App\Validator\TagName\TagNameConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class MysqlKeywordConstraintValidator extends ConstraintValidator
{
    public function __construct(
        private readonly MysqlKeyWordFilterInterface $mysqlKeyWordFilter
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof MysqlKeywordConstraint) {
            throw new UnexpectedTypeException($constraint, MysqlKeywordConstraint::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if ('' === $value) {
            return;
        }

        if ($this->mysqlKeyWordFilter->isMysqlKeyword($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }

}