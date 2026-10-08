<?php

declare(strict_types=1);

namespace App\Domain\LetterFeature\Factory;

use App\Domain\LetterFeature\Enum\LetterStatus;
use App\Domain\LetterFeature\Model\LetterInterface;

/**
 * Фабрика для создания буквы.
 */
interface LetterFactoryInterface
{
    /**
     * Создаст букву.
     *
     * @param int          $guessId Идентификатор догадки.
     * @param int          $index   Номер буквы в слове.
     * @param string       $value   Значение буквы.
     * @param LetterStatus $status  Статус буквы.
     *
     * @return LetterInterface
     */
    public function create(
        int $guessId,
        int $index,
        string $value,
        LetterStatus $status,
    ): LetterInterface;
}
