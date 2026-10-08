<?php

declare(strict_types=1);

namespace App\Infrastructure\Common\Http\V1\Responder;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Формирование тела ошибок JSON:API по результатам валидации.
 */
class ValidationExceptionResponder
{
    /**
     * Название ошибки.
     */
    private const VALIDATION_ERROR_TITLE = 'Ошибка валидации';

    /**
     * Создаст тело ошибки валидации.
     *
     * @param JsonApiErrorResponder $jsonApiErrorResponder Формирование одной ошибки JSON:API (поля status, title, detail).
     */
    public function __construct(
        readonly private JsonApiErrorResponder $jsonApiErrorResponder
    ) {
    }

    /**
     * Преобразует список нарушений в массив ошибок JSON:API.
     *
     * @param ConstraintViolationListInterface $violations Результат валидации Symfony.
     *
     * @return array<int, array<string, string>>
     */
    public function compose(ConstraintViolationListInterface $violations): array
    {
        $errors = [];

        foreach ($violations as $violation) {
            $errors[] = $this->jsonApiErrorResponder->compose(
                status: (string) Response::HTTP_BAD_REQUEST,
                title: self::VALIDATION_ERROR_TITLE,
                detail: (string) $violation->getMessage(),
            );
        }

        return $errors;
    }
}
