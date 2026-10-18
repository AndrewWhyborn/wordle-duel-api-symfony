<?php

declare(strict_types=1);

namespace App\Application\GameFeature\Process;

use App\Domain\GameFeature\Enum\GameStatus;
use App\Domain\GameFeature\Exception\GameCreationConflictException;
use App\Domain\GameFeature\Factory\GameFactoryInterface;
use App\Domain\GameFeature\Model\GameInterface;
use App\Domain\GameFeature\Repository\GameRepositoryInterface;
use App\Domain\GameFeature\Storage\CreateGameStorageInterface;
use App\Domain\SecretFeature\Exception\SecretNotFoundException;
use App\Domain\SecretFeature\Repository\SecretRepositoryInterface;

/**
 * Процесс создания игры.
 */
readonly class CreateGameProcess
{
    /**
     * Создаст процесс.
     *
     * @param GameFactoryInterface       $gameFactory       Фабрика для создания сущности игры.
     * @param CreateGameStorageInterface $createGameStorage Служба сохранения игры.
     * @param SecretRepositoryInterface  $secretRepository  Репозиторий для чтения загаданных слов.
     * @param GameRepositoryInterface    $gameRepository    Репозиторий для чтения игр.
     */
    public function __construct(
        private GameFactoryInterface $gameFactory,
        private CreateGameStorageInterface $createGameStorage,
        private SecretRepositoryInterface $secretRepository,
        private GameRepositoryInterface $gameRepository,
    ) {
    }

    /**
     * Создаст игру.
     *
     * @param string $secretId Идентификатор загаданного слова.
     * @param string $ip       IP игрока.
     *
     * @return GameInterface
     *
     * @throws SecretNotFoundException
     * @throws GameCreationConflictException
     */
    public function execute(string $secretId, string $ip): GameInterface
    {
        $secret = $this->secretRepository->findById($secretId);
        if ($secret === null) {
            throw new SecretNotFoundException();
        }

        $existedGame = $this->gameRepository->findLastBySecretIdAndIp($secretId, $ip);

        if ($existedGame !== null) {
            $canRetryFailedGame
                = $existedGame->getStatus() === GameStatus::FAILED
                && $secret->isAllowRetry() === true
            ;

            if (!$canRetryFailedGame) {
                $message = match ($existedGame->getStatus()) {
                    GameStatus::COMPLETED => 'Вы уже победили в этой игре.',
                    GameStatus::FAILED => 'Вы проиграли в этой игре.',
                    default => 'Игра уже создана.',
                };

                throw new GameCreationConflictException($message);
            }
        }

        $game = $this->gameFactory->create(
            secretId: $secretId,
            ip: $ip,
            status: GameStatus::IN_PROGRESS,
        );

        return $this->createGameStorage->save($game);
    }
}

