<?php

declare(strict_types=1);

namespace App\Infrastructure\DictionaryFeature\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Проверка, что значение является реальным словом.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class WordExists extends Constraint
{
    /**
     * Сообщение при неуспешной валидации.
     */
    public string $message = 'Слово «{{ value }}» не найдено в словаре, попробуйте другое.';
}
