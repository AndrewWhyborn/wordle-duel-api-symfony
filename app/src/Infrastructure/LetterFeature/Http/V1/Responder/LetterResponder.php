<?php

declare(strict_types=1);

namespace App\Infrastructure\LetterFeature\Http\V1\Responder;

use App\Domain\LetterFeature\Model\LetterInterface;
use App\Infrastructure\LetterFeature\Http\V1\Enum\LetterResourceType;

/**
 * Формирование ответа JSON:API для буквы.
 */
class LetterResponder
{
    /**
     * Собирает resource object буквы (для data / included).
     *
     * @param LetterInterface $letter Буква.
     *
     * @return array{
     *     type: string,
     *     id: string,
     *     attributes: array{
     *         guessId: string|null,
     *         index: int|null,
     *         value: string|null,
     *         status: string|null,
     *         createdAt: string|null,
     *         updatedAt: string|null
     *     }
     * }
     */
    public function composeResource(LetterInterface $letter): array
    {
        return [
            'type' => LetterResourceType::LETTERS->value,
            'id' => (string) $letter->getId(),
            'attributes' => [
                'guessId' => $letter->getGuessId(),
                'index' => $letter->getIndex(),
                'value' => $letter->getValue(),
                'status' => $letter->getStatus()?->value,
                'createdAt' => $letter->getCreatedAt()?->format(\DateTimeInterface::ATOM),
                'updatedAt' => $letter->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
            ],
        ];
    }
}
