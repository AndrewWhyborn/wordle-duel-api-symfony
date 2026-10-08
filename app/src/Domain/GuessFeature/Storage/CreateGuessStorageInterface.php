<?php

declare(strict_types=1);

namespace App\Domain\GuessFeature\Storage;

use App\Domain\GuessFeature\Model\GuessInterface;

/**
 * Служба сохранения догадки.
 */
interface CreateGuessStorageInterface
{
    /**
     * Сохранит догадку и вернёт её с идентификатором из базы.
     *
     * @param GuessInterface $guess Догадка.
     *
     * @return GuessInterface
     */
    public function save(GuessInterface $guess): GuessInterface;
}
