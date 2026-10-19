<?php

declare(strict_types=1);

namespace App\Infrastructure\Common\JsonEncoding;

use JsonException;

/**
 * Преобразователь значений в формат JSON.
 */
final class JsonEncoder
{
    /**
     * Флаги, с которыми по умолчанию будет выполняться преобразование.
     *
     * См. https://www.php.net/manual/ru/json.constants.php
     */
    public const DEFAULT_OPTIONS
        = JSON_THROW_ON_ERROR
        | JSON_INVALID_UTF8_SUBSTITUTE // Преобразовывать недопустимые символы UTF-8 в \0xfffd.
        | JSON_PRESERVE_ZERO_FRACTION // Значение типа float будет преобразовано именно во float если дробная часть равна 0.
        | JSON_UNESCAPED_SLASHES // Не экранировать «/».
        | JSON_UNESCAPED_UNICODE // Не кодировать многобайтовые символы Unicode как \uXXXX.
    ;

    /**
     * Преобразует значение в формат JSON.
     *
     * @param mixed $value Преобразуемое значение.
     *
     * @return string Строка в формате JSON.
     *
     * @throws JsonException
     */
    public static function encode(mixed $value): string
    {
        if (!\is_array($value) && !\is_object($value)) {
            throw new JsonException('Несовместимый с форматом JSON тип.');
        }

        if ($value === []) {
            return '{}';
        }

        return (string) \json_encode($value, self::DEFAULT_OPTIONS);
    }
}
