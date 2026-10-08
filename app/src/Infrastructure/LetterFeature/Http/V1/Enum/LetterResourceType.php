<?php

declare(strict_types=1);

namespace App\Infrastructure\LetterFeature\Http\V1\Enum;

/**
 * Тип ресурса буквы в JSON:API.
 */
enum LetterResourceType: string
{
    case LETTERS = 'letters';
}
