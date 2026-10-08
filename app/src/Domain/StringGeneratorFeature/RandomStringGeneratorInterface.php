<?php

declare(strict_types=1);

namespace App\Domain\StringGeneratorFeature;

/**
 * Генератор строк.
 */
interface RandomStringGeneratorInterface
{
    /**
     * Сгенерирует строку заданной длины.
     *
     * @param int $length Длина строки.
     *
     * @return string
     */
    public function generate(int $length): string;
}
