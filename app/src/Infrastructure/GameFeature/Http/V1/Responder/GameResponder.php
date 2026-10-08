<?php

declare(strict_types=1);

namespace App\Infrastructure\GameFeature\Http\V1\Responder;

use App\Application\GuessFeature\DTO\GuessWithLetters;
use App\Domain\GameFeature\Model\GameInterface;
use App\Infrastructure\GameFeature\Http\V1\Enum\GameResourceType;
use App\Infrastructure\GuessFeature\Http\V1\Enum\GuessResourceType;
use App\Infrastructure\GuessFeature\Http\V1\Responder\GuessResponder;
use App\Infrastructure\LetterFeature\Http\V1\Responder\LetterResponder;

/**
 * Формирование ответа JSON:API для игры.
 */
class GameResponder
{
    /**
     * Создаст responder.
     *
     * @param GuessResponder  $guessResponder  Формирование resource object догадки.
     * @param LetterResponder $letterResponder Формирование resource object буквы.
     */
    public function __construct(
        private readonly GuessResponder $guessResponder,
        private readonly LetterResponder $letterResponder,
    ) {
    }

    /**
     * Собирает JSON:API-документ для одной игры.
     *
     * Догадки и буквы передаются через relationships + included (compound document JSON:API).
     *
     * @param GameInterface              $game    Игра.
     * @param list<GuessWithLetters>|null $guesses Догадки с буквами; null — без relationships.
     *
     * @return array{
     *     data: array{
     *         type: string,
     *         id: string,
     *         attributes: array{
     *             status: string|null,
     *             createdAt: string|null,
     *             updatedAt: string|null
     *         },
     *         relationships?: array{
     *             guesses: array{data: list<array{type: string, id: string}>}
     *         }
     *     },
     *     included?: list<array<string, mixed>>
     * }
     */
    public function composeOne(GameInterface $game, ?array $guesses = null): array
    {
        $document = [
            'data' => [
                'type' => GameResourceType::GAMES->value,
                'id' => (string) $game->getId(),
                'attributes' => [
                    'status' => $game->getStatus()?->value,
                    'createdAt' => $game->getCreatedAt()?->format(\DateTimeInterface::ATOM),
                    'updatedAt' => $game->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
                ],
            ],
        ];

        if ($guesses === null) {
            return $document;
        }

        $document['data']['relationships'] = [
            'guesses' => [
                'data' => \array_map(
                    static fn (GuessWithLetters $item): array => [
                        'type' => GuessResourceType::GUESSES->value,
                        'id' => (string) $item->guess->getId(),
                    ],
                    $guesses,
                ),
            ],
        ];

        $included = [];
        foreach ($guesses as $item) {
            $included[] = $this->guessResponder->composeResource($item->guess, $item->letters);
            foreach ($item->letters as $letter) {
                $included[] = $this->letterResponder->composeResource($letter);
            }
        }

        if ($included !== []) {
            $document['included'] = $included;
        }

        return $document;
    }
}
