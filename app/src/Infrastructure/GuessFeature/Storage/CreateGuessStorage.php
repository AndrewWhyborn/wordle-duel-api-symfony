<?php

declare(strict_types=1);

namespace App\Infrastructure\GuessFeature\Storage;

use App\Domain\GuessFeature\Model\GuessInterface;
use App\Domain\GuessFeature\Storage\CreateGuessStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Служба сохранения догадки через EntityManager Doctrine.
 */
readonly class CreateGuessStorage implements CreateGuessStorageInterface
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
    public function save(GuessInterface $guess): GuessInterface
    {
        $this->entityManager->persist($guess);
        $this->entityManager->flush();

        return $guess;
    }
}
