<?php

declare(strict_types=1);

namespace App\Application\GameFeature\Service;

use App\Application\GameFeature\DTO\GameWithGuesses;
use App\Application\GuessFeature\DTO\GuessWithLetters;
use App\Domain\GameFeature\Exception\GameNotFoundException;
use App\Domain\GameFeature\Repository\GameRepositoryInterface;
use App\Domain\GuessFeature\Repository\GuessRepositoryInterface;
use App\Domain\LetterFeature\Repository\LetterRepositoryInterface;

/**
 * Служба получения игры.
 */
readonly class GetGameService
{
    /**
     * Создаст службу.
     *
     * @param GameRepositoryInterface   $gameRepository   Репозиторий для чтения игр.
     * @param GuessRepositoryInterface  $guessRepository  Репозиторий для чтения догадок.
     * @param LetterRepositoryInterface $letterRepository Репозиторий для чтения букв.
     */
    public function __construct(
        private GameRepositoryInterface $gameRepository,
        private GuessRepositoryInterface $guessRepository,
        private LetterRepositoryInterface $letterRepository,
    ) {
    }

    /**
     * Вернёт игру с догадками и буквами по идентификатору загаданного слова и IP игрока.
     *
     * @param string $secretId Идентификатор загаданного слова.
     * @param string $ip       IP игрока.
     *
     * @return GameWithGuesses
     *
     * @throws GameNotFoundException
     */
    public function execute(string $secretId, string $ip): GameWithGuesses
    {
        $game = $this->gameRepository->findLastBySecretIdAndIp($secretId, $ip);

        if ($game === null) {
            throw new GameNotFoundException();
        }

        $guesses = $this->guessRepository->findOrderedByGameId((int) $game->getId());
        $guessIds = \array_values(\array_filter(
            \array_map(static fn ($guess): ?int => $guess->getId(), $guesses),
            static fn (?int $id): bool => $id !== null,
        ));

        $lettersByGuessId = [];
        foreach ($this->letterRepository->findOrderedByGuessIds($guessIds) as $letter) {
            $lettersByGuessId[(int) $letter->getGuessId()][] = $letter;
        }

        return new GameWithGuesses(
            game: $game,
            guesses: \array_map(
                static fn ($guess): GuessWithLetters => new GuessWithLetters(
                    guess: $guess,
                    letters: $lettersByGuessId[(int) $guess->getId()] ?? [],
                ),
                $guesses,
            ),
        );
    }
}
