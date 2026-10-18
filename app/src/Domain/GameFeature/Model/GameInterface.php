<?php

declare(strict_types=1);

namespace App\Domain\GameFeature\Model;

use App\Domain\GameFeature\Enum\GameStatus;
use App\Domain\GameFeature\Exception\InvalidGameStatusTransitionException;

/**
 * Игра.
 */
interface GameInterface
{
    /**
     * Идентификатор игры.
     */
    public function getId(): ?string;

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
     * Установит статус игры.
     *
     * @param GameStatus $status Статус игры.
     *
     * @throws InvalidGameStatusTransitionException
     */
    public function setStatus(GameStatus $status): void;

    /**
     * Дата создания.
     */
    public function getCreatedAt(): ?\DateTimeImmutable;

    /**
     * Дата обновления.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable;
}
