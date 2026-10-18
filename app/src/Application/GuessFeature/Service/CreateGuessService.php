<?php

declare(strict_types=1);

namespace App\Application\GuessFeature\Service;

use App\Domain\GameFeature\Model\GameInterface;
use App\Domain\GuessFeature\Factory\GuessFactoryInterface;
use App\Domain\GuessFeature\Model\GuessInterface;
use App\Domain\GuessFeature\Repository\GuessRepositoryInterface;
use App\Domain\GuessFeature\Storage\CreateGuessStorageInterface;

/**
 * Служба создания догадки.
 */
readonly class CreateGuessService
{
    /**
     * Создаст службу.
     *
     * @param GuessFactoryInterface       $guessFactory       Фабрика для создания сущности догадки.
     * @param GuessRepositoryInterface    $guessRepository    Репозиторий для чтения догадок.
     * @param CreateGuessStorageInterface $createGuessStorage Служба сохранения догадки.
     */
    public function __construct(
        private GuessFactoryInterface $guessFactory,
        private GuessRepositoryInterface $guessRepository,
        private CreateGuessStorageInterface $createGuessStorage,
    ) {
    }

    /**
     * Создаст и сохранит догадку для игры.
     *
     * @param GameInterface $game Игра.
     *
     * @return GuessInterface
     */
    public function execute(GameInterface $game): GuessInterface
    {
        $gameId = (string) $game->getId();
        $index = $this->guessRepository->countByGameId($gameId) + 1;

        return $this->createGuessStorage->save(
            $this->guessFactory->create($gameId, $index),
        );
    }
}
