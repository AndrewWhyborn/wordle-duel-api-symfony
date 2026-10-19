<?php

declare(strict_types=1);

namespace App\Application\GameFeature\Service;

use App\Domain\GameFeature\Enum\GameStatus;
use App\Domain\GameFeature\Model\GameInterface;
use App\Domain\GameFeature\Storage\UpdateGameStorageInterface;

/**
 * Служба смены статуса игры.
 */
readonly class ChangeGameStatusService
{
    /**
     * Создаст службу.
     *
     * @param UpdateGameStorageInterface $updateGameStorage Служба сохранения изменений игры.
     */
    public function __construct(
        private UpdateGameStorageInterface $updateGameStorage,
    ) {
    }

    /**
     * Установит статус игры и сохранит изменение.
     *
     * @param GameInterface $game   Игра.
     * @param GameStatus    $status Новый статус.
     *
     * @return GameInterface
     */
    public function execute(GameInterface $game, GameStatus $status): GameInterface
    {
        $game->setStatus($status);

        return $this->updateGameStorage->update($game);
    }
}
