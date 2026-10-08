<?php

declare(strict_types=1);

namespace App\Infrastructure\GuessFeature\Http\V1\Responder;

use App\Domain\GuessFeature\Model\GuessInterface;
use App\Domain\LetterFeature\Model\LetterInterface;
use App\Infrastructure\GuessFeature\Http\V1\Enum\GuessResourceType;
use App\Infrastructure\LetterFeature\Http\V1\Enum\LetterResourceType;
use App\Infrastructure\LetterFeature\Http\V1\Responder\LetterResponder;

/**
 * Формирование ответа JSON:API для догадки.
 */
class GuessResponder
{
    /**
     * Создаст responder.
     *
     * @param LetterResponder $letterResponder Формирование resource object буквы.
     */
    public function __construct(
        private readonly LetterResponder $letterResponder,
    ) {
    }

    /**
     * Собирает resource object догадки (для data / included).
     *
     * @param GuessInterface             $guess   Догадка.
     * @param list<LetterInterface>|null $letters Буквы; null — без relationships.
     *
     * @return array<string, mixed>
     */
    public function composeResource(GuessInterface $guess, ?array $letters = null): array
    {
        $resource = [
            'type' => GuessResourceType::GUESSES->value,
            'id' => (string) $guess->getId(),
            'attributes' => [
                'gameId' => $guess->getGameId(),
                'index' => $guess->getIndex(),
                'createdAt' => $guess->getCreatedAt()?->format(\DateTimeInterface::ATOM),
                'updatedAt' => $guess->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
            ],
        ];

        if ($letters === null) {
            return $resource;
        }

        $resource['relationships'] = [
            'letters' => [
                'data' => \array_map(
                    static fn (LetterInterface $letter): array => [
                        'type' => LetterResourceType::LETTERS->value,
                        'id' => (string) $letter->getId(),
                    ],
                    $letters,
                ),
            ],
        ];

        return $resource;
    }

    /**
     * Собирает JSON:API-документ для одной догадки.
     *
     * @param GuessInterface             $guess   Догадка.
     * @param list<LetterInterface>|null $letters Буквы догадки; null — без relationships.
     *
     * @return array{data: array<string, mixed>, included?: list<array<string, mixed>>}
     */
    public function composeOne(GuessInterface $guess, ?array $letters = null): array
    {
        $document = [
            'data' => $this->composeResource($guess, $letters),
        ];

        if ($letters !== null && $letters !== []) {
            $document['included'] = \array_map(
                fn (LetterInterface $letter): array => $this->letterResponder->composeResource($letter),
                $letters,
            );
        }

        return $document;
    }
}
