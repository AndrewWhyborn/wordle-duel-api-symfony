<?php

declare(strict_types=1);

namespace App\Infrastructure\Common\Http\V1\Responder;

/**
 * Формирование одной ошибки JSON:API (поля status, title, detail).
 */
class JsonApiErrorResponder
{
    /**
     * Формирует один объект ошибки JSON:API.
     *
     * @param string $status HTTP-код ошибки строкой.
     * @param string $title  Краткий заголовок.
     * @param string $detail Текст для пользователя.
     *
     * @return array<string, string>
     */
    public function compose(string $status, string $title, string $detail): array
    {
        return [
            'status' => $status,
            'title' => $title,
            'detail' => $detail,
        ];
    }
}
