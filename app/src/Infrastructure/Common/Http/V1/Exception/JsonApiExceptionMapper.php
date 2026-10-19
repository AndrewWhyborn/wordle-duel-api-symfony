<?php

declare(strict_types=1);

namespace App\Infrastructure\Common\Http\V1\Exception;

use App\Domain\DictionaryFeature\Exception\WordNotFoundInDictionaryException;
use App\Domain\GameFeature\Exception\GameCreationConflictException;
use App\Domain\GameFeature\Exception\GameNotFoundException;
use App\Domain\GameFeature\Exception\GameNotInProgressException;
use App\Domain\GameFeature\Exception\InvalidGameStatusTransitionException;
use App\Domain\GuessFeature\Exception\GuessNotFoundException;
use App\Domain\SecretFeature\Exception\SecretNotFoundException;
use App\Infrastructure\Common\Http\V1\Responder\JsonApiErrorResponder;
use App\Infrastructure\Common\Http\V1\Responder\ValidationExceptionResponder;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Преобразует исключения API в JSON:API-ответ.
 */
readonly class JsonApiExceptionMapper
{
    /**
     * Создаст mapper.
     *
     * @param JsonApiErrorResponder        $jsonApiErrorResponder        Формирование одной ошибки JSON:API.
     * @param ValidationExceptionResponder $validationExceptionResponder Формирование тела ошибок валидации.
     * @param LoggerInterface              $logger                       Служба журналирования.
     */
    public function __construct(
        private JsonApiErrorResponder $jsonApiErrorResponder,
        private ValidationExceptionResponder $validationExceptionResponder,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Соберёт JSON:API-ответ по исключению.
     *
     * @param \Throwable $exception Исключение.
     */
    public function map(\Throwable $exception): JsonResponse
    {
        if ($exception instanceof ValidatorException) {
            return $this->jsonResponse(
                status: Response::HTTP_BAD_REQUEST,
                data: ['errors' => $this->validationExceptionResponder->compose($exception->violations)],
            );
        }

        if ($exception instanceof \JsonException) {
            return $this->errorResponse(
                status: Response::HTTP_BAD_REQUEST,
                title: 'Ошибка валидации',
                detail: 'Тело запроса должно быть корректным JSON.',
            );
        }

        $mapping = $this->resolveMapping($exception);
        if ($mapping !== null) {
            [$status, $title] = $mapping;

            return $this->errorResponse($status, $title, $exception->getMessage());
        }

        $this->logger->critical(
            message: 'Непредвиденная ошибка API.',
            context: [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ],
        );

        return $this->errorResponse(
            status: Response::HTTP_INTERNAL_SERVER_ERROR,
            title: 'Непредвиденная ошибка',
            detail: 'Произошла непредвиденная ошибка.',
        );
    }

    /**
     * @return array{0: int, 1: string}|null
     */
    private function resolveMapping(\Throwable $exception): ?array
    {
        return match (true) {
            $exception instanceof WordNotFoundInDictionaryException => [
                Response::HTTP_BAD_REQUEST,
                'Ошибка валидации',
            ],
            $exception instanceof GameNotFoundException,
            $exception instanceof SecretNotFoundException,
            $exception instanceof GuessNotFoundException => [
                Response::HTTP_NOT_FOUND,
                'Не найдено',
            ],
            $exception instanceof GameCreationConflictException,
            $exception instanceof GameNotInProgressException,
            $exception instanceof InvalidGameStatusTransitionException => [
                Response::HTTP_CONFLICT,
                'Конфликт.',
            ],
            default => null,
        };
    }

    private function errorResponse(int $status, string $title, string $detail): JsonResponse
    {
        return $this->jsonResponse(
            status: $status,
            data: [
                'errors' => [
                    $this->jsonApiErrorResponder->compose(
                        status: (string) $status,
                        title: $title,
                        detail: $detail,
                    ),
                ],
            ],
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function jsonResponse(int $status, array $data): JsonResponse
    {
        return new JsonResponse(
            data: $data,
            status: $status,
            headers: ['Content-Type' => 'application/json'],
        );
    }
}
