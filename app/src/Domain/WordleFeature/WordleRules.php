<?php

declare(strict_types=1);

namespace App\Domain\WordleFeature;

/**
 * Правила Wordle: длина слова и число попыток.
 */
interface WordleRules
{
    /**
     * Количество букв в слове.
     */
    public const WORD_LENGTH = 5;

    /**
     * Максимальное количество догадок (ходов).
     */
    public const MAX_GUESSES = 6;
}
