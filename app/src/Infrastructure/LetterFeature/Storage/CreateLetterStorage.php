<?php

declare(strict_types=1);

namespace App\Infrastructure\LetterFeature\Storage;

use App\Domain\LetterFeature\Model\LetterInterface;
use App\Domain\LetterFeature\Storage\CreateLetterStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Служба сохранения букв через EntityManager Doctrine.
 */
readonly class CreateLetterStorage implements CreateLetterStorageInterface
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
    public function saveAll(array $letters): array
    {
        foreach ($letters as $letter) {
            $this->entityManager->persist($letter);
        }

        $this->entityManager->flush();

        return $letters;
    }
}
