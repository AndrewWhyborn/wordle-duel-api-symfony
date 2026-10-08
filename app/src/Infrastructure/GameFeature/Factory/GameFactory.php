<?php

declare(strict_types=1);

namespace App\Infrastructure\GameFeature\Factory;

use App\Domain\GameFeature\Enum\GameStatus;
use App\Domain\GameFeature\Factory\GameFactoryInterface;
use App\Domain\GameFeature\Model\GameInterface;
use App\Infrastructure\GameFeature\Doctrine\Entity\Game;

/**
 * @inheritDoc
 */
class GameFactory implements GameFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(string $secretId, string $ip, GameStatus $status = GameStatus::IN_PROGRESS): GameInterface
    {
        return new Game($secretId, $ip, $status);
    }
}
