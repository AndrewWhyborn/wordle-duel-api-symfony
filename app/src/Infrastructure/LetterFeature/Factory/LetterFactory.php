<?php

declare(strict_types=1);

namespace App\Infrastructure\LetterFeature\Factory;

use App\Domain\LetterFeature\Enum\LetterStatus;
use App\Domain\LetterFeature\Factory\LetterFactoryInterface;
use App\Domain\LetterFeature\Model\LetterInterface;
use App\Infrastructure\LetterFeature\Doctrine\Entity\Letter;

/**
 * @inheritDoc
 */
class LetterFactory implements LetterFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(
        int $guessId,
        int $index,
        string $value,
        LetterStatus $status,
    ): LetterInterface {
        return new Letter($guessId, $index, $value, $status);
    }
}
