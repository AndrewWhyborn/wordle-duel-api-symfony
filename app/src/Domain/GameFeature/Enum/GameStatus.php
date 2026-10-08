<?php

declare(strict_types=1);

namespace App\Domain\GameFeature\Enum;

/**
 * Состояние игры.
 */
enum GameStatus: string
{
    /**
     * Игра в процессе.
     */
    case IN_PROGRESS = 'in_progress';

    /**
     * Игрок победил.
     */
    case COMPLETED = 'completed';

    /**
     * Игрок проиграл.
     */
    case FAILED = 'failed';
}
