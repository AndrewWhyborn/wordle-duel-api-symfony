<?php

declare(strict_types=1);

namespace App\Application\GuessFeature\DTO;

use App\Domain\GuessFeature\Model\GuessInterface;
use App\Domain\LetterFeature\Model\LetterInterface;

/**
 * Догадка вместе с буквами.
 */
readonly class GuessWithLetters
{
    /**
     * @param GuessInterface        $guess   Догадка.
     * @param list<LetterInterface> $letters Буквы догадки.
     */
    public function __construct(
        public GuessInterface $guess,
        public array $letters,
    ) {
    }
}
