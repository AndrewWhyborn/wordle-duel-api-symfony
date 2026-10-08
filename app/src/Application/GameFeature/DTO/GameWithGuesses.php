<?php

declare(strict_types=1);

namespace App\Application\GameFeature\DTO;

use App\Application\GuessFeature\DTO\GuessWithLetters;
use App\Domain\GameFeature\Model\GameInterface;

/**
 * Игра вместе с догадками и буквами.
 */
readonly class GameWithGuesses
{
    /**
     * @param GameInterface         $game    Игра.
     * @param list<GuessWithLetters> $guesses Догадки с буквами.
     */
    public function __construct(
        public GameInterface $game,
        public array $guesses,
    ) {
    }
}
