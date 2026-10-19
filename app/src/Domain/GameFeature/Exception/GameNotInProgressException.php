<?php

declare(strict_types=1);

namespace App\Domain\GameFeature\Exception;

/**
 * Игра существует, но уже не в статусе «в процессе».
 */
class GameNotInProgressException extends \RuntimeException
{
    public function __construct(string $message = 'Игра уже закончена.')
    {
        parent::__construct($message);
    }
}
