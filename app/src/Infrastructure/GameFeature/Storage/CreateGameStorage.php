<?php

declare(strict_types=1);

namespace App\Infrastructure\GameFeature\Storage;

use App\Domain\GameFeature\Model\GameInterface;
use App\Domain\GameFeature\Storage\CreateGameStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Служба сохранения игры через EntityManager Doctrine.
 */
readonly class CreateGameStorage implements CreateGameStorageInterface
{
    /**
     * Создаст службу сохранения.
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
    public function save(GameInterface $game): GameInterface
    {
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $game;
    }
}
