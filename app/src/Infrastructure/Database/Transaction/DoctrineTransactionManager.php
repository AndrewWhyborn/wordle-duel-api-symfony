<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Transaction;

use App\Domain\Common\Transaction\TransactionManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Менеджер транзакций на базе Doctrine EntityManager.
 */
readonly class DoctrineTransactionManager implements TransactionManagerInterface
{
    /**
     * Создаст менеджер транзакций.
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
    public function transactional(callable $callback): mixed
    {
        return $this->entityManager->wrapInTransaction($callback);
    }
}
