<?php

declare(strict_types=1);

namespace App\Domain\DictionaryFeature\Exception;

/**
 * Слово не найдено в словаре.
 */
class WordNotFoundInDictionaryException extends \RuntimeException
{
    public function __construct(string $word)
    {
        parent::__construct(
            \sprintf('Слово «%s» не найдено в словаре, попробуйте другое.', $word),
        );
    }
}
