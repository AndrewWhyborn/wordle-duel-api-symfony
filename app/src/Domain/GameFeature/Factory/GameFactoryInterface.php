<?php

declare(strict_types=1);

namespace App\Domain\GameFeature\Factory;

use App\Domain\GameFeature\Enum\GameStatus;
use App\Domain\GameFeature\Model\GameInterface;

/**
 * Фабрика для создания игры.
 */
interface GameFactoryInterface
{
    /**
     * Создаст игру.
     *
     * @param string     $secretId Идентификатор загаданного слова.
     * @param string     $ip       IP игрока.
     * @param GameStatus $status   Статус игры.
     *
     * @return GameInterface
     */
    public function create(string $secretId, string $ip, GameStatus $status = GameStatus::IN_PROGRESS): GameInterface;
}
