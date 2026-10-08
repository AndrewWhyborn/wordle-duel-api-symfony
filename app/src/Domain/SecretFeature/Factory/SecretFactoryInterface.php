<?php

declare(strict_types=1);

namespace App\Domain\SecretFeature\Factory;

use App\Domain\SecretFeature\Model\SecretInterface;

/**
 * Фабрика для создания загаданное слова.
 */
interface SecretFactoryInterface
{
    /**
     * Создаст загаданное слово.
     *
     * @param string $id         Уникальный идентификатор записи.
     * @param string $value      Значение загаданного слова.
     * @param bool   $isRealWord Является ли слово реальным (следует ли проверять в словаре).
     * @param bool   $allowRetry Разрешено ли переиграть.
     *
     * @return SecretInterface
     */
    public function create(string $id, string $value, bool $isRealWord, bool $allowRetry): SecretInterface;
}
