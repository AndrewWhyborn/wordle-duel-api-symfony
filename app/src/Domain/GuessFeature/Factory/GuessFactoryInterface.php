<?php

declare(strict_types=1);

namespace App\Domain\GuessFeature\Factory;

use App\Domain\GuessFeature\Model\GuessInterface;

/**
 * Фабрика для создания догадки.
 */
interface GuessFactoryInterface
{
    /**
     * Создаст догадку.
     *
     * @param int $gameId Идентификатор игры.
     * @param int $index  Номер догадки в игре.
     *
     * @return GuessInterface
     */
    public function create(int $gameId, int $index): GuessInterface;
}
