<?php

declare(strict_types=1);

namespace App\Domain\SecretFeature\Model;

/**
 * Загаданное слово.
 */
interface SecretInterface
{
    /**
     * Идентификатор записи.
     */
    public function getId(): ?string;

    /**
     * Значение загаданного слова.
     */
    public function getValue(): ?string;

    /**
     * Является ли слово реальным (следует ли проверять в словаре).
     */
    public function isRealWord(): ?bool;

    /**
     * Разрешено ли переиграть.
     */
    public function isAllowRetry(): ?bool;

    /**
     * Дата создания.
     */
    public function getCreatedAt(): ?\DateTimeImmutable;
}
