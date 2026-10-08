<?php

declare(strict_types=1);

namespace App\Domain\Common\Transaction;

/**
 * Менеджер транзакций.
 */
interface TransactionManagerInterface
{
    /**
     * Выполнит callback внутри транзакции.
     *
     * @template T
     *
     * @param callable(): T $callback Код, выполняемый в транзакции.
     *
     * @return T
     */
    public function transactional(callable $callback): mixed;
}
