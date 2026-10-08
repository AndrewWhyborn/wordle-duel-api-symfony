<?php

declare(strict_types=1);

namespace App\Infrastructure\StringGeneratorFeature;

use App\Domain\StringGeneratorFeature\RandomStringGeneratorInterface;

/**
 * Генератор строк.
 */
class RandomStringGenerator implements RandomStringGeneratorInterface
{
    /**
     * @inheritDoc
     */
    public function generate(int $length): string
    {
        if ($length <= 0) {
            throw new \InvalidArgumentException('Длина генерируемой строки не может равняться 0.');
        }

        $string = '';

        while (($len = \strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = \random_bytes($size);
            $string .= \substr(\str_replace(['/', '+', '='], '', \base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}
