<?php

declare(strict_types=1);

namespace App\Infrastructure\SecretFeature\Http\V1\Controller;

use App\Infrastructure\Common\Http\Middleware\XAuthTokenMiddleware;
use App\Infrastructure\Common\Http\V1\Exception\ValidatorException;
use App\Infrastructure\SecretFeature\Http\V1\ACL\Adaptor\CreateSecretHttpAdapter;
use App\Infrastructure\SecretFeature\Http\V1\Responder\SecretResponder;
use JsonException;
use Kafkiansky\SymfonyMiddleware\Attribute\Middleware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Контроллер для сохранения загаданного слова.
 */
#[Middleware([XAuthTokenMiddleware::class])]
final readonly class CreateSecretController
{
    /**
     * Создаст контроллер.
     *
     * @param CreateSecretHttpAdapter $createSecretHttpAdapter Валидатор HTTP-тела запроса перед сохранением загаданного слова.
     * @param SecretResponder         $secretResponder         Формирование ответа JSON:API для загаданного слова.
     */
    public function __construct(
        private CreateSecretHttpAdapter $createSecretHttpAdapter,
        private SecretResponder $secretResponder,
    ) {
    }

    /**
     * Сохранит загаданное слово.
     *
     * @param Request $request HTTP-запрос.
     *
     * @throws ValidatorException|JsonException
     */
    #[Route(
        path: '/api/v1/secrets',
        name: 'api_v1_create_secret',
        methods: [Request::METHOD_POST]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $secret = $this->createSecretHttpAdapter->execute($request);

        return new JsonResponse(
            data: $this->secretResponder->composeOne($secret),
            status: Response::HTTP_CREATED,
            headers: ['Content-Type' => 'application/json'],
        );
    }
}
