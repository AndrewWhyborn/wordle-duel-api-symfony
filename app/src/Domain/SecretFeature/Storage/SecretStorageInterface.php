<?php

declare(strict_types=1);

namespace App\Domain\SecretFeature\Storage;

use App\Domain\SecretFeature\Model\SecretInterface;

/**
 * Служба для сохранения загаданного слова.
 */
interface SecretStorageInterface
{
    /**
     * Сохранит загаданное слово.
     *
     * @param SecretInterface $secret Загаданное слово.
     *
     * @return void
     */
    public function save(SecretInterface $secret): void;
}
