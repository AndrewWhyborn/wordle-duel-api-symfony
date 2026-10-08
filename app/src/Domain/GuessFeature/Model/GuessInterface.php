<?php

declare(strict_types=1);

namespace App\Domain\GuessFeature\Model;

/**
 * Догадка.
 */
interface GuessInterface
{
    /**
     * Идентификатор догадки.
     */
    public function getId(): ?int;

    /**
     * Идентификатор игры.
     */
    public function getGameId(): ?int;

    /**
     * Номер догадки в игре.
     */
    public function getIndex(): ?int;

    /**
     * Дата создания.
     */
    public function getCreatedAt(): ?\DateTimeImmutable;

    /**
     * Дата обновления.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable;
}
