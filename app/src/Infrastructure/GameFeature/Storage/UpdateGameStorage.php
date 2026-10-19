<?php

declare(strict_types=1);

namespace App\Infrastructure\GameFeature\Storage;

use App\Domain\GameFeature\Model\GameInterface;
use App\Domain\GameFeature\Storage\UpdateGameStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Служба обновления игры через EntityManager Doctrine.
 */
readonly class UpdateGameStorage implements UpdateGameStorageInterface
{
    /**
     * Создаст службу обновления.
     *
     * @param EntityManagerInterface $entityManager Менеджер сущностей ORM.
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function update(GameInterface $game): GameInterface
    {
        $this->entityManager->flush();

        return $game;
    }
}
