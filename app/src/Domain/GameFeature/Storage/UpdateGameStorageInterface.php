<?php

declare(strict_types=1);

namespace App\Domain\GameFeature\Storage;

use App\Domain\GameFeature\Model\GameInterface;

/**
 * Служба обновления игры.
 */
interface UpdateGameStorageInterface
{
    /**
     * Сохранит изменения игры.
     *
     * @param GameInterface $game Игра.
     *
     * @return GameInterface
     */
    public function update(GameInterface $game): GameInterface;
}
