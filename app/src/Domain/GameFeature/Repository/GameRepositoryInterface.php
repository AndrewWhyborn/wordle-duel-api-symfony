<?php

declare(strict_types=1);

namespace App\Domain\GameFeature\Repository;

use App\Domain\GameFeature\Model\GameInterface;

/**
 * Репозиторий для чтения игр.
 */
interface GameRepositoryInterface
{
    /**
     * Найдет игру по идентификатору.
     *
     * @param int $id Идентификатор игры.
     *
     * @return GameInterface|null
     */
    public function findById(int $id): ?GameInterface;

    /**
     * Найдет последнюю игру по идентификатору загаданного слова и IP игрока.
     *
     * @param string $secretId Идентификатор загаданного слова.
     * @param string $ip       IP игрока.
     *
     * @return GameInterface|null
     */
    public function findLastBySecretIdAndIp(string $secretId, string $ip): ?GameInterface;
}
