<?php

declare(strict_types=1);

namespace App\Infrastructure\GameFeature\Doctrine\Repository;

use App\Domain\GameFeature\Model\GameInterface;
use App\Domain\GameFeature\Repository\GameRepositoryInterface;
use App\Infrastructure\GameFeature\Doctrine\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * Репозиторий для чтения игр из БД.
 */
class GameRepository extends ServiceEntityRepository implements GameRepositoryInterface
{
    /**
     * Создаст репозиторий.
     *
     * @param ManagerRegistry $registry Реестр менеджеров сущностей Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /**
     * @inheritDoc
     */
    public function findById(string $id): ?GameInterface
    {
        return $this->find(Uuid::fromString($id));
    }

    /**
     * @inheritDoc
     */
    public function findLastBySecretIdAndIp(string $secretId, string $ip): ?GameInterface
    {
        return $this->createQueryBuilder('game')
            ->andWhere('game.secretId = :secretId')
            ->andWhere('game.ip = :ip')
            ->setParameter('secretId', $secretId)
            ->setParameter('ip', $ip)
            ->orderBy('game.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
