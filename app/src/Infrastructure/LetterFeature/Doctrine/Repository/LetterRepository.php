<?php

declare(strict_types=1);

namespace App\Infrastructure\LetterFeature\Doctrine\Repository;

use App\Domain\LetterFeature\Model\LetterInterface;
use App\Domain\LetterFeature\Repository\LetterRepositoryInterface;
use App\Infrastructure\LetterFeature\Doctrine\Entity\Letter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Репозиторий для чтения букв из БД.
 */
class LetterRepository extends ServiceEntityRepository implements LetterRepositoryInterface
{
    /**
     * Создаст репозиторий.
     *
     * @param ManagerRegistry $registry Реестр менеджеров сущностей Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Letter::class);
    }

    /**
     * @inheritDoc
     */
    public function findOrderedByGuessIds(array $guessIds): array
    {
        if ($guessIds === []) {
            return [];
        }

        /** @var list<LetterInterface> $letters */
        $letters = $this->createQueryBuilder('letter')
            ->andWhere('letter.guessId IN (:guessIds)')
            ->setParameter('guessIds', $guessIds)
            ->orderBy('letter.guessId', 'ASC')
            ->addOrderBy('letter.index', 'ASC')
            ->getQuery()
            ->getResult();

        return $letters;
    }
}
