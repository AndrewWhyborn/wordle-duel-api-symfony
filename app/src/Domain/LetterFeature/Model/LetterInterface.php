<?php

declare(strict_types=1);

namespace App\Domain\LetterFeature\Model;

use App\Domain\LetterFeature\Enum\LetterStatus;

/**
 * Буква догадки.
 */
interface LetterInterface
{
    /**
     * Идентификатор буквы.
     */
    public function getId(): ?int;

    /**
     * Идентификатор догадки.
     */
    public function getGuessId(): ?int;

    /**
     * Номер буквы в слове.
     */
    public function getIndex(): ?int;

    /**
     * Значение буквы.
     */
    public function getValue(): ?string;

    /**
     * Статус буквы.
     */
    public function getStatus(): ?LetterStatus;

    /**
     * Дата создания.
     */
    public function getCreatedAt(): ?\DateTimeImmutable;

    /**
     * Дата обновления.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable;
}
