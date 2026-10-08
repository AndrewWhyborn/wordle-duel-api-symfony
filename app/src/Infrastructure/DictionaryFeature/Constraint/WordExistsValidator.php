<?php

declare(strict_types=1);

namespace App\Infrastructure\DictionaryFeature\Constraint;

use App\Domain\DictionaryFeature\Service\CheckWordExistenceServiceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Валидатор {@see WordExists}.
 */
final class WordExistsValidator extends ConstraintValidator
{
    public function __construct(
        readonly private CheckWordExistenceServiceInterface $checkWordExistenceService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof WordExists) {
            throw new UnexpectedTypeException($constraint, WordExists::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if ($this->checkWordExistenceService->execute($value) === true) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', (string) $value)
            ->addViolation();
    }
}
