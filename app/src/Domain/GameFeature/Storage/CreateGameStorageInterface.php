<?php

declare(strict_types=1);

namespace App\Domain\GameFeature\Storage;

use App\Domain\GameFeature\Model\GameInterface;

/**
 * Служба сохранения игры.
 */
interface CreateGameStorageInterface
{
    /**
     * Сохранит игру и вернёт её с идентификатором из базы.
     *
     * @param GameInterface $game Игра.
     *
     * @return GameInterface
     */
    public function save(GameInterface $game): GameInterface;
}
