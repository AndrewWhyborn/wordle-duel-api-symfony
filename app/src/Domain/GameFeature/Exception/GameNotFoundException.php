<?php

declare(strict_types=1);

namespace App\Domain\GameFeature\Exception;

/**
 * Игра не найдена.
 */
class GameNotFoundException extends \RuntimeException
{
    public function __construct(string $message = 'Игра не найдена.')
    {
        parent::__construct($message);
    }
}
