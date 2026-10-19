<?php

declare(strict_types=1);

namespace App\Domain\SecretFeature\Exception;

/**
 * Загаданное слово не найдено.
 */
class SecretNotFoundException extends \RuntimeException
{
    public function __construct(string $message = 'Загаданное слово не найдено.')
    {
        parent::__construct($message);
    }
}
