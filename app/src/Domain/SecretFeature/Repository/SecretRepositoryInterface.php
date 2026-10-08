<?php

declare(strict_types=1);

namespace App\Domain\SecretFeature\Repository;

use App\Domain\SecretFeature\Model\SecretInterface;

/**
 * Репозиторий для чтения загаданных слов.
 */
interface SecretRepositoryInterface
{
    /**
     * Найдет загаданное слово по идентификатору.
     *
     * @param string $id Идентификатор загаданного слова.
     *
     * @return SecretInterface|null
     */
    public function findById(string $id): ?SecretInterface;
}
