<?php

declare(strict_types=1);

namespace App\Domain\GuessFeature\Exception;

/**
 * Догадка не найдена.
 */
class GuessNotFoundException extends \RuntimeException
{
    public function __construct(string $message = 'Догадка не найдена.')
    {
        parent::__construct($message);
    }
}
