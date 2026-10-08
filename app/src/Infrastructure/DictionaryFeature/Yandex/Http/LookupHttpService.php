<?php

declare(strict_types=1);

namespace App\Infrastructure\DictionaryFeature\Yandex\Http;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Служба для поиска слова в Яндекс Словаре.
 */
readonly class LookupHttpService extends AbstractYandexDictionaryHttpService
{
    /**
     * HTTP метод.
     */
    private const METHOD = 'GET';

    /**
     * HTTP маршрут.
     */
    private const ROUTE = 'lookup';

    /**
     * Найдет слово в Яндекс Словаре.
     *
     * @param string $text Слово.
     *
     * @return array|null
     *
     * @throws \Exception|TransportExceptionInterface
     */
    public function execute(string $text): ?array
    {
        $params = [
            'lang' => 'ru-en',
            'text' => $text,
        ];

        $responseData = $this->sendRequest(
            method: self::METHOD,
            uri: self::ROUTE,
            params: $params,
        );

        return $responseData['def'] ?? null;
    }
}
