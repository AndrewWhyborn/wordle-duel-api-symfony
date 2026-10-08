<?php

declare(strict_types=1);

namespace App\Domain\GameFeature\Exception;

/**
 * Конфликт при создании игры.
 */
class GameCreationConflictException extends \RuntimeException
{
    public function __construct(string $message = 'Конфликт при создании игры.')
    {
        parent::__construct($message, 409);
    }
}
