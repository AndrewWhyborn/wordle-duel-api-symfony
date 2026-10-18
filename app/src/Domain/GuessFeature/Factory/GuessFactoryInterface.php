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
     * @param string $gameId Идентификатор игры.
     * @param int    $index  Номер догадки в игре.
     *
     * @return GuessInterface
     */
    public function create(string $gameId, int $index): GuessInterface;
}
