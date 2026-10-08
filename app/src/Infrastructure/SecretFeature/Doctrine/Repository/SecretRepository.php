<?php

declare(strict_types=1);

namespace App\Infrastructure\SecretFeature\Doctrine\Repository;

use App\Domain\SecretFeature\Model\SecretInterface;
use App\Domain\SecretFeature\Repository\SecretRepositoryInterface;
use App\Infrastructure\SecretFeature\Doctrine\Entity\Secret;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Репозиторий для чтения загаданных слов из БД.
 */
class SecretRepository extends ServiceEntityRepository implements SecretRepositoryInterface
{
    /**
     * Создаст репозиторий.
     *
     * @param ManagerRegistry $registry Реестр менеджеров сущностей Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Secret::class);
    }

    /**
     * @inheritDoc
     */
    public function findById(string $id): ?SecretInterface
    {
        return $this->find($id);
    }
}
