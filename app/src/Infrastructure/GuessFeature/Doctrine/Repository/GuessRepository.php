<?php

declare(strict_types=1);

namespace App\Infrastructure\GuessFeature\Doctrine\Repository;

use App\Domain\GuessFeature\Model\GuessInterface;
use App\Domain\GuessFeature\Repository\GuessRepositoryInterface;
use App\Infrastructure\GuessFeature\Doctrine\Entity\Guess;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Репозиторий для чтения догадок из БД.
 */
class GuessRepository extends ServiceEntityRepository implements GuessRepositoryInterface
{
    /**
     * Создаст репозиторий.
     *
     * @param ManagerRegistry $registry Реестр менеджеров сущностей Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Guess::class);
    }

    /**
     * @inheritDoc
     */
    public function findOrderedByGameId(int $gameId): array
    {
        /** @var list<GuessInterface> $guesses */
        $guesses = $this->createQueryBuilder('guess')
            ->andWhere('guess.gameId = :gameId')
            ->setParameter('gameId', $gameId)
            ->orderBy('guess.index', 'ASC')
            ->getQuery()
            ->getResult();

        return $guesses;
    }

    /**
     * @inheritDoc
     */
    public function countByGameId(int $gameId): int
    {
        return (int) $this->createQueryBuilder('guess')
            ->select('COUNT(guess.id)')
            ->andWhere('guess.gameId = :gameId')
            ->setParameter('gameId', $gameId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
