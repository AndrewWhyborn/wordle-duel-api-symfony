<?php

declare(strict_types=1);

namespace App\Infrastructure\GuessFeature\Http\V1\Enum;

/**
 * Тип ресурса догадки в JSON:API.
 */
enum GuessResourceType: string
{
    case GUESSES = 'guesses';
}
