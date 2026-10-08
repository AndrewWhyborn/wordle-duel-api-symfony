<?php

declare(strict_types=1);

namespace App\Domain\DictionaryFeature\Service;

/**
 * Служба проверки существования слова в словаре.
 */
interface CheckWordExistenceServiceInterface
{
    /**
     * Проверит, существует ли слово в словаре.
     *
     * @param string $word Слово для проверки.
     *
     * @return bool
     */
    public function execute(string $word): bool;
}
