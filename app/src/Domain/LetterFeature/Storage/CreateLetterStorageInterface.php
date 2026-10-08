<?php

declare(strict_types=1);

namespace App\Domain\LetterFeature\Storage;

use App\Domain\LetterFeature\Model\LetterInterface;

/**
 * Служба сохранения букв.
 */
interface CreateLetterStorageInterface
{
    /**
     * Сохранит набор букв.
     *
     * @param list<LetterInterface> $letters Буквы.
     *
     * @return list<LetterInterface>
     */
    public function saveAll(array $letters): array;
}
