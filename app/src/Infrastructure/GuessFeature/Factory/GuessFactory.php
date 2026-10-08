<?php

declare(strict_types=1);

namespace App\Infrastructure\GuessFeature\Factory;

use App\Domain\GuessFeature\Factory\GuessFactoryInterface;
use App\Domain\GuessFeature\Model\GuessInterface;
use App\Infrastructure\GuessFeature\Doctrine\Entity\Guess;

/**
 * @inheritDoc
 */
class GuessFactory implements GuessFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(int $gameId, int $index): GuessInterface
    {
        return new Guess($gameId, $index);
    }
}
