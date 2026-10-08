<?php

declare(strict_types=1);

namespace App\Infrastructure\SecretFeature\Http\V1\Enum;

/**
 * Тип ресурса загаданного слова в JSON:API.
 */
enum SecretResourceType: string
{
    case SECRETS = 'secrets';
}
