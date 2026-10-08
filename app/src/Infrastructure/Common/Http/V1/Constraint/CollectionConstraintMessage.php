<?php

declare(strict_types=1);

namespace App\Infrastructure\Common\Http\V1\Constraint;

/**
 * Общие сообщения Symfony Collection для валидации JSON:API-тел.
 */
final class CollectionConstraintMessage
{
    /**
     * Ошибка неожиданного атрибута.
     */
    public const EXTRA = 'Неожиданный атрибут «{{ field }}».';

    /**
     * Ошибка недостающего атрибута.
     */
    public const MISSING = 'Отсутствует обязательный атрибут «{{ field }}».';
}
