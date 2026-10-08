<?php

declare(strict_types=1);

namespace App\Domain\GuessFeature\Repository;

use App\Domain\GuessFeature\Model\GuessInterface;

/**
 * Репозиторий для чтения догадок.
 */
interface GuessRepositoryInterface
{
    /**
     * Найдет догадки игры, отсортированные по номеру.
     *
     * @param int $gameId Идентификатор игры.
     *
     * @return list<GuessInterface>
     */
    public function findOrderedByGameId(int $gameId): array;

    /**
     * Вернёт количество догадок в игре.
     *
     * @param int $gameId Идентификатор игры.
     *
     * @return int
     */
    public function countByGameId(int $gameId): int;
}
