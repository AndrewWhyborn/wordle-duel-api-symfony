<?php

declare(strict_types=1);

namespace App\Domain\SecretFeature\Exception;

/**
 * Не удалось сгенерировать уникальный идентификатор секрета.
 */
class SecretIdGenerationFailedException extends \RuntimeException
{
    public function __construct(string $message = 'Не удалось сгенерировать уникальный идентификатор загаданного слова.')
    {
        parent::__construct($message);
    }
}
