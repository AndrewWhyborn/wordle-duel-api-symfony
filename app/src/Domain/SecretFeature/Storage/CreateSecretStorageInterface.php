<?php

declare(strict_types=1);

namespace App\Domain\SecretFeature\Storage;

use App\Domain\SecretFeature\Model\SecretInterface;

/**
 * Служба сохранения загаданного слова.
 */
interface CreateSecretStorageInterface
{
    /**
     * Сохранит загаданное слово и вернёт его с идентификатором из базы.
     *
     * @param SecretInterface $secret Загаданное слово.
     *
     * @return SecretInterface
     */
    public function save(SecretInterface $secret): SecretInterface;
}
