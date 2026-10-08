<?php

declare(strict_types=1);

namespace App\Infrastructure\GameFeature\Http\V1\Enum;

/**
 * Тип ресурса игры в JSON:API.
 */
enum GameResourceType: string
{
    case GAMES = 'games';
}
