<?php

declare(strict_types=1);

namespace App\Infrastructure\SecretFeature\Storage;

use App\Domain\SecretFeature\Model\SecretInterface;
use App\Domain\SecretFeature\Storage\CreateSecretStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Служба сохранения загаданного слова через EntityManager Doctrine.
 */
readonly class CreateSecretStorage implements CreateSecretStorageInterface
{
    /**
     * Создаст службу сохранения.
     *
     * @param EntityManagerInterface $entityManager Менеджер сущностей ORM.
     */
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @inheritDoc
     */
    public function save(SecretInterface $secret): SecretInterface
    {
        $this->entityManager->persist($secret);
        $this->entityManager->flush();

        return $secret;
    }
}
