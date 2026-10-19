<?php

declare(strict_types=1);

namespace App\Infrastructure\GameFeature\Http\V1\Controller;

use App\Infrastructure\Common\Http\Middleware\XAuthTokenMiddleware;
use App\Infrastructure\Common\Http\V1\Exception\ValidatorException;
use App\Infrastructure\GameFeature\Http\V1\ACL\Adaptor\CreateGameHttpAdapter;
use App\Infrastructure\GameFeature\Http\V1\Responder\GameResponder;
use JsonException;
use Kafkiansky\SymfonyMiddleware\Attribute\Middleware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Контроллер для создания игры.
 */
#[Middleware([XAuthTokenMiddleware::class])]
final readonly class CreateGameController
{
    /**
     * Создаст контроллер.
     *
     * @param CreateGameHttpAdapter $createGameHttpAdapter Адаптер HTTP-тела запроса перед созданием игры.
     * @param GameResponder         $gameResponder         Формирование ответа JSON:API для игры.
     */
    public function __construct(
        private CreateGameHttpAdapter $createGameHttpAdapter,
        private GameResponder $gameResponder,
    ) {
    }

    /**
     * Создаст игру.
     *
     * @param Request $request HTTP-запрос.
     *
     * @throws ValidatorException|JsonException
     */
    #[Route(
        path: '/api/v1/games',
        name: 'api_v1_create_game',
        methods: [Request::METHOD_POST]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $game = $this->createGameHttpAdapter->execute($request);

        return new JsonResponse(
            data: $this->gameResponder->composeOne($game),
            status: Response::HTTP_CREATED,
            headers: ['Content-Type' => 'application/json'],
        );
    }
}
