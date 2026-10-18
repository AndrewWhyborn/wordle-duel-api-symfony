<?php

declare(strict_types=1);

namespace App\Domain\GameFeature\Exception;

/**
 * Недопустимая смена статуса игры.
 */
class InvalidGameStatusTransitionException extends \RuntimeException
{
    public function __construct(string $message = 'Статус игры можно изменить только из состояния «в процессе».')
    {
        parent::__construct($message, 409);
    }
}
