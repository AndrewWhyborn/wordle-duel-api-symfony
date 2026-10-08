<?php

declare(strict_types=1);

namespace App\Application\GuessFeature\Process;

use App\Application\GuessFeature\DTO\GuessWithLetters;
use App\Application\GuessFeature\Service\CreateGuessService;
use App\Domain\Common\Transaction\TransactionManagerInterface;
use App\Domain\GameFeature\Enum\GameStatus;
use App\Domain\GameFeature\Exception\GameNotFoundException;
use App\Domain\GameFeature\Model\GameInterface;
use App\Domain\GameFeature\Repository\GameRepositoryInterface;
use App\Domain\LetterFeature\Enum\LetterStatus;
use App\Domain\LetterFeature\Factory\LetterFactoryInterface;
use App\Domain\LetterFeature\Storage\CreateLetterStorageInterface;
use App\Infrastructure\GameFeature\Doctrine\Entity\Game;

/**
 * Процесс создания догадки.
 */
readonly class CreateGuessProcess
{
    /**
     * Создаст процесс.
     *
     * @param GameRepositoryInterface      $gameRepository      Репозиторий для чтения игр.
     * @param CreateGuessService           $createGuessService  Служба создания догадки.
     * @param LetterFactoryInterface       $letterFactory       Фабрика для создания сущности буквы.
     * @param CreateLetterStorageInterface $createLetterStorage Служба сохранения букв.
     * @param TransactionManagerInterface  $transactionManager  Менеджер транзакций.
     */
    public function __construct(
        private GameRepositoryInterface $gameRepository,
        private CreateGuessService $createGuessService,
        private LetterFactoryInterface $letterFactory,
        private CreateLetterStorageInterface $createLetterStorage,
        private TransactionManagerInterface $transactionManager,
    ) {
    }

    /**
     * Создаст догадку вместе с буквами в одной транзакции.
     *
     * @param int          $gameId  Идентификатор игры.
     * @param list<string> $letters Введённые буквы.
     *
     * @return GuessWithLetters
     *
     * @throws GameNotFoundException
     */
    public function execute(int $gameId, array $letters): GuessWithLetters
    {
        $game = $this->gameRepository->findById($gameId);

        if (
            $game === null
            || $game->getStatus()->value !== GameStatus::IN_PROGRESS->value
        ) {
            throw new GameNotFoundException();
        }

        return $this->transactionManager->transactional(
            fn (): GuessWithLetters => $this->performExecute($game, $letters),
        );
    }

    /**
     * Создаст догадку и связанные буквы.
     *
     * @param GameInterface $game    Игра.
     * @param list<string>  $letters Введённые буквы.
     *
     * @return GuessWithLetters
     */
    private function performExecute(GameInterface $game, array $letters): GuessWithLetters
    {
        $guess = $this->createGuessService->execute($game);
        $guessId = (int) $guess->getId();
        $letterEntities = [];

        foreach ($letters as $letterIndex => $value) {
            $letterEntities[] = $this->letterFactory->create(
                guessId: $guessId,
                index: $letterIndex + 1,
                value: \mb_strtoupper($value),
                status: LetterStatus::FAR,
            );
        }

        return new GuessWithLetters(
            guess: $guess,
            letters: $this->createLetterStorage->saveAll($letterEntities),
        );
    }
}
