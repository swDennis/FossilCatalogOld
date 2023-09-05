<?php

namespace App\Validator\TagName;

use App\Repository\TagRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class TagNameConstraintValidator extends ConstraintValidator
{
    public function __construct(
        private readonly TagRepositoryInterface $tagRepository
    ) {
    }


    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof TagNameConstraint) {
            throw new UnexpectedTypeException($constraint, TagNameConstraint::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$constraint->tagInfo->isValidationRequired()) {
            return;
        }

        if ($this->checkTagNameExists($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }

    private function checkTagNameExists(string $tagName): bool
    {
        foreach ($this->tagRepository->getList(TagRepositoryInterface::GET_ALL) as $savedTag) {
            if (strtolower($tagName) !== strtolower($savedTag['name'])) {
                continue;
            }

            return true;
        }

        return false;
    }
}