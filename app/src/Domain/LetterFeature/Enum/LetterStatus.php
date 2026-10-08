<?php

declare(strict_types=1);

namespace App\Domain\LetterFeature\Enum;

/**
 * Статус буквы.
 */
enum LetterStatus: string
{
    /**
     * Буква отсутствует в слове.
     */
    case FAR = 'far';

    /**
     * Буква присутствует в слове на другой позиции.
     */
    case NEAR = 'near';

    /**
     * Буква на правильной позиции.
     */
    case SUCCESS = 'success';
}
