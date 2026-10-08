<?php

declare(strict_types=1);

namespace App\Infrastructure\Common\Http\V1\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Исключение при ошибке валидации входных данных.
 */
class ValidatorException extends \Exception
{
    /**
     * Создаст исключение.
     *
     * @param ConstraintViolationListInterface $violations Список нарушений валидации.
     */
    public function __construct(public readonly ConstraintViolationListInterface $violations) {
        parent::__construct();
    }
}
