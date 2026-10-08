<?php

declare(strict_types=1);

namespace App\Infrastructure\SecretFeature\Http\V1\Responder;

use App\Domain\SecretFeature\Model\SecretInterface;
use App\Infrastructure\SecretFeature\Http\V1\Enum\SecretResourceType;

/**
 * Формирование ответа JSON:API для загаданного слова.
 */
class SecretResponder
{
    /**
     * Собирает JSON:API-документ для одного загаданного слова.
     *
     * @param SecretInterface $secret Загаданное слово.
     *
     * @return array{
     *     data: array{
     *         type: string,
     *         id: string,
     *         attributes: array{
     *             value: string|null,
     *             isRealWord: bool|null,
     *             allowRetry: bool|null,
     *             createdAt: string|null
     *         }
     *     }
     * }
     */
    public function composeOne(SecretInterface $secret): array
    {
        return [
            'data' => [
                'type' => SecretResourceType::SECRETS->value,
                'id' => (string) $secret->getId(),
                'attributes' => [
                    'value' => $secret->getValue(),
                    'isRealWord' => $secret->isRealWord(),
                    'allowRetry' => $secret->isAllowRetry(),
                    'createdAt' => $secret->getCreatedAt()?->format(\DateTimeInterface::ATOM),
                ],
            ],
        ];
    }
}
