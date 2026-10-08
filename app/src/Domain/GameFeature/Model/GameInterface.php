<?php

declare(strict_types=1);

namespace App\Domain\GameFeature\Model;

use App\Domain\GameFeature\Enum\GameStatus;

/**
 * Игра.
 */
interface GameInterface
{
    /**
     * Идентификатор игры.
     */
    public function getId(): ?int;

    /**
     * Идентификатор загаданного слова.
     */
    public function getSecretId(): ?string;

    /**
     * IP игрока.
     */
    public function getIp(): ?string;

    /**
     * Статус игры.
     */
    public function getStatus(): ?GameStatus;

    /**
     * Дата создания.
     */
    public function getCreatedAt(): ?\DateTimeImmutable;

    /**
     * Дата обновления.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable;
}
