<?php

declare(strict_types=1);

namespace App\Infrastructure\TokenFeature\Service;

use App\Domain\TokenFeature\Service\CheckAvailableTokenServiceInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/**
 * Служба для проверки наличия токена в таблице.
 */
readonly class CheckAvailableTokenService implements CheckAvailableTokenServiceInterface
{
    /**
     * Создаст службу.
     *
     * @param Connection $connection Соединение с базой данных.
     */
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function execute(string $tableName, string $token, string $field = 'token'): bool
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $result
            = $queryBuilder
            ->select('1')
            ->from($tableName)
            ->where($queryBuilder->expr()->eq($field, ':token'))
            ->setParameter('token', $token)
            ->setMaxResults(1)
            ->fetchOne()
        ;

        return $result !== false && $result !== null;
    }
}
