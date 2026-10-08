<?php

declare(strict_types=1);

namespace App\Infrastructure\DictionaryFeature\Yandex\Http;

use App\Infrastructure\Common\JsonEncoding\JsonDecoder;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Абстрактная служба для отправки HTTP запросов в Яндекс Словарь.
 */
readonly class AbstractYandexDictionaryHttpService
{
    /**
     * Создаст службу.
     *
     * @param LoggerInterface     $logger      Служба журналирования.
     * @param HttpClientInterface $httpClient  Служба HTTP запросов.
     * @param JsonDecoder         $jsonDecoder Преобразователь строк формата JSON.
     * @param string              $apiKey      АПИ-ключ.
     */
    public function __construct(
        private LoggerInterface $logger,
        private HttpClientInterface $httpClient,
        private JsonDecoder $jsonDecoder,
        private string $apiKey,
    ) {
    }

    /**
     * Отправит запрос.
     *
     * @param string $method HTTP метод.
     * @param string $uri    HTTP адрес.
     * @param array  $params Параметры запроса.
     *
     * @return array
     *
     * @throws \Exception|TransportExceptionInterface
     */
    public function sendRequest(
        string $method,
        string $uri,
        array $params,
    ): array {
        try {
            $query = \array_merge(['key' => $this->apiKey], $params);

            $this->logger->debug(
                \sprintf(
                    'Отправляем запрос "%s %s" ...',
                    $method,
                    $uri,
                ),
                ['query' => $query],
            );

            $response = $this->httpClient->request($method, $uri, [
                'query' => $query,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            $rawContents = $response->getContent(false);

            $this->logger->debug(
                \sprintf(
                    'На запрос "%s %s" получен ответ. Статус "%s".',
                    $method,
                    $uri,
                    $response->getStatusCode(),
                ),
                [
                    'contents' => $rawContents,
                ],
            );

            $responseData = $this->jsonDecoder->decode($rawContents);

            if (isset($responseData['error']) && $responseData['error'] !== '') {
                throw new \Exception((string) $responseData['error']);
            }

            return $responseData;
        } catch (\Exception $exception) {
            $this->logger->error(
                $message = \sprintf(
                    'Ошибка запроса %s %s. %s',
                    $method,
                    $uri,
                    $exception->getMessage(),
                ),
                [
                    'code' => $exception instanceof TransportExceptionInterface ? $exception->getCode() : null,
                    'trace' => $exception->getTrace(),
                ],
            );

            throw new \Exception($message, previous: $exception);
        }
    }
}
