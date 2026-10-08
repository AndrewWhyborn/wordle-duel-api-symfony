<?php

declare(strict_types=1);

namespace App\Domain\TokenFeature\Service;

/**
 * Служба для проверки наличия токена в таблице.
 */
interface CheckAvailableTokenServiceInterface
{
    /**
     * Проверит наличие токена в таблице.
     *
     * @param string $tableName Название таблицы.
     * @param string $token     Токен.
     * @param string $field     Атрибут таблицы в котором хранится уникальный токен.
     *
     * @return bool
     */
    public function execute(string $tableName, string $token, string $field = 'token'): bool;
}
