<?php

declare(strict_types=1);

namespace App\Domain\LetterFeature\Repository;

use App\Domain\LetterFeature\Model\LetterInterface;

/**
 * Репозиторий для чтения букв.
 */
interface LetterRepositoryInterface
{
    /**
     * Найдет буквы догадок, отсортированные по догадке и номеру.
     *
     * @param list<int> $guessIds Идентификаторы догадок.
     *
     * @return list<LetterInterface>
     */
    public function findOrderedByGuessIds(array $guessIds): array;
}
