<?php

declare(strict_types=1);

namespace App\Domain\SecretFeature\Service;

use App\Domain\SecretFeature\Exception\SecretIdGenerationFailedException;

/**
 * Генерация уникального идентификатора загаданного слова.
 */
interface GenerateUniqueSecretIdInterface
{
    /**
     * Сгенерирует свободный идентификатор секрета.
     *
     * @throws SecretIdGenerationFailedException
     */
    public function execute(): string;
}
