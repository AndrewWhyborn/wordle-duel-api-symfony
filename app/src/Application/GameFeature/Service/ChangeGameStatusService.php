<?php

declare(strict_types=1);

namespace App\Application\GameFeature\Service;

use App\Domain\GameFeature\Enum\GameStatus;
use App\Domain\GameFeature\Model\GameInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Служба смены статуса игры.
 */
readonly class ChangeGameStatusService
{
    /**
     * Создаст службу.
     *
     * @param EntityManagerInterface $entityManager Менеджер сущностей ORM.
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
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
        $this->entityManager->flush();

        return $game;
    }
}
