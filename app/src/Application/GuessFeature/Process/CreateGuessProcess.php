<?php

declare(strict_types=1);

namespace App\Application\GuessFeature\Process;

use App\Application\GameFeature\Service\ChangeGameStatusService;
use App\Application\GuessFeature\DTO\GuessWithLetters;
use App\Application\GuessFeature\Service\CreateGuessService;
use App\Domain\DictionaryFeature\Exception\WordNotFoundInDictionaryException;
use App\Domain\DictionaryFeature\Service\CheckWordExistenceServiceInterface;
use App\Domain\GameFeature\Enum\GameStatus;
use App\Domain\GameFeature\Exception\GameNotFoundException;
use App\Domain\GameFeature\Exception\GameNotInProgressException;
use App\Domain\GameFeature\Model\GameInterface;
use App\Domain\GameFeature\Repository\GameRepositoryInterface;
use App\Domain\LetterFeature\Enum\LetterStatus;
use App\Domain\LetterFeature\Factory\LetterFactoryInterface;
use App\Domain\LetterFeature\Storage\CreateLetterStorageInterface;
use App\Domain\Common\Transaction\TransactionManagerInterface;
use App\Domain\SecretFeature\Exception\SecretNotFoundException;
use App\Domain\SecretFeature\Model\SecretInterface;
use App\Domain\SecretFeature\Repository\SecretRepositoryInterface;
use App\Domain\WordleFeature\WordleMatcher;
use App\Domain\WordleFeature\WordleRules;

/**
 * Процесс создания догадки.
 */
readonly class CreateGuessProcess
{
    /**
     * Создаст процесс.
     *
     * @param GameRepositoryInterface               $gameRepository                 Репозиторий для чтения игр.
     * @param CreateGuessService                    $createGuessService             Служба создания догадки.
     * @param WordleMatcher                         $wordleMatcher                  Сравнение букв с загаданным словом.
     * @param LetterFactoryInterface                $letterFactory                  Фабрика для создания сущности буквы.
     * @param CreateLetterStorageInterface          $createLetterStorage            Служба сохранения букв.
     * @param ChangeGameStatusService               $changeGameStatusService        Служба смены статуса игры.
     * @param TransactionManagerInterface           $transactionManager             Менеджер транзакций.
     * @param SecretRepositoryInterface             $secretRepository               Репозиторий секретных слов.
     * @param CheckWordExistenceServiceInterface    $checkWordExistenceService      Служба проверки слова в словаре.
     */
    public function __construct(
        private GameRepositoryInterface $gameRepository,
        private CreateGuessService $createGuessService,
        private WordleMatcher $wordleMatcher,
        private LetterFactoryInterface $letterFactory,
        private CreateLetterStorageInterface $createLetterStorage,
        private ChangeGameStatusService $changeGameStatusService,
        private TransactionManagerInterface $transactionManager,
        private SecretRepositoryInterface $secretRepository,
        private CheckWordExistenceServiceInterface $checkWordExistenceService,
    ) {
    }

    /**
     * Создаст догадку вместе с буквами в одной транзакции.
     *
     * @param string       $gameId       Идентификатор игры.
     * @param list<string> $guessLetters Введённые буквы.
     *
     * @return GuessWithLetters
     *
     * @throws GameNotFoundException
     * @throws GameNotInProgressException
     * @throws SecretNotFoundException
     * @throws WordNotFoundInDictionaryException
     */
    public function execute(
        string $gameId,
        array $guessLetters
    ): GuessWithLetters {
        $game = $this->gameRepository->findById($gameId);

        if ($game === null) {
            throw new GameNotFoundException();
        }

        if ($game->getStatus() !== GameStatus::IN_PROGRESS) {
            throw new GameNotInProgressException();
        }

        $secret = $this->secretRepository->findById($game->getSecretId());
        if ($secret === null) {
            throw new SecretNotFoundException();
        }

        if ($secret->isRealWord() === true) {
            $word = \mb_strtolower(\implode('', $guessLetters));

            if ($this->checkWordExistenceService->execute($word) !== true) {
                throw new WordNotFoundInDictionaryException($word);
            }
        }

        return $this->transactionManager->transactional(
            fn (): GuessWithLetters => $this->performExecute($game, $secret, $guessLetters),
        );
    }

    /**
     * Создаст догадку и связанные буквы.
     *
     * @param GameInterface   $game         Игра.
     * @param SecretInterface $secret       Загаданное слово.
     * @param list<string>    $guessLetters Введённые буквы.
     *
     * @return GuessWithLetters
     */
    private function performExecute(
        GameInterface $game,
        SecretInterface $secret,
        array $guessLetters
    ): GuessWithLetters {
        $guess = $this->createGuessService->execute($game);
        $guessId = (string) $guess->getId();

        $statuses = $this->wordleMatcher->execute(
            secretLetters: \mb_str_split((string) $secret->getValue()),
            guessLetters: $guessLetters,
        );
        $letterEntities = [];

        foreach ($guessLetters as $letterIndex => $value) {
            $letterEntities[] = $this->letterFactory->create(
                guessId: $guessId,
                index: $letterIndex + 1,
                value: \mb_strtoupper($value),
                status: $statuses[$letterIndex],
            );
        }

        if ($this->isAllSuccess($statuses)) {
            $this->changeGameStatusService->execute($game, GameStatus::COMPLETED);
        } elseif ($guess->getIndex() === WordleRules::MAX_GUESSES) {
            $this->changeGameStatusService->execute($game, GameStatus::FAILED);
        }

        return new GuessWithLetters(
            guess: $guess,
            letters: $this->createLetterStorage->saveAll($letterEntities),
        );
    }

    /**
     * Все ли буквы имеют статус SUCCESS.
     *
     * @param list<LetterStatus> $statuses Статусы букв.
     */
    private function isAllSuccess(array $statuses): bool
    {
        if ($statuses === []) {
            return false;
        }

        foreach ($statuses as $status) {
            if ($status !== LetterStatus::SUCCESS) {
                return false;
            }
        }

        return true;
    }
}
