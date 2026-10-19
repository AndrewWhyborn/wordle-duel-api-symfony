<?php

declare(strict_types=1);

namespace App\Infrastructure\Common\JsonEncoding;

use JsonException;

/**
 * Преобразователь строк формата JSON.
 */
final class JsonDecoder
{
    /**
     * Флаги, с которыми по умолчанию будет выполняться преобразование.
     *
     * См. https://www.php.net/manual/ru/json.constants.php
     */
    public const DEFAULT_OPTIONS
        = JSON_THROW_ON_ERROR
        | JSON_BIGINT_AS_STRING // Декодирует большие целые числа как строки.
        | JSON_INVALID_UTF8_SUBSTITUTE // Преобразовывать недопустимые символы UTF-8 в \0xfffd.
    ;

    /**
     * Преобразует строку JSON в значение PHP.
     *
     * @param string $json Строка в формате JSON.
     *
     * @return array
     *
     * @throws JsonException
     */
    public static function decode(string $json): array
    {
        try {
            $value = \json_decode($json, true, 512, self::DEFAULT_OPTIONS);
        } catch (\Throwable $exception) {
            throw new JsonException(
                \sprintf(
                    'Во время преобразования JSON произошла ошибка "%s".',
                    $exception->getMessage(),
                ),
            );
        }

        if (!\is_array($value)) {
            throw new JsonException('Ожидался JSON-объект или массив.');
        }

        return $value;
    }
}
