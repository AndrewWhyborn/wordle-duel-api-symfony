<?php

declare(strict_types=1);

namespace App\Infrastructure\GameFeature\Http\V1\Controller;

use App\Infrastructure\Common\Http\Middleware\XAuthTokenMiddleware;
use App\Infrastructure\Common\Http\V1\Exception\ValidatorException;
use App\Infrastructure\GameFeature\Http\V1\ACL\Adaptor\GetGameHttpAdapter;
use App\Infrastructure\GameFeature\Http\V1\Responder\GameResponder;
use Kafkiansky\SymfonyMiddleware\Attribute\Middleware;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Контроллер для получения игры.
 */
#[Middleware([XAuthTokenMiddleware::class])]
final readonly class GetGameController
{
    /**
     * Создаст контроллер.
     *
     * @param GetGameHttpAdapter $getGameHttpAdapter Адаптер query-параметров перед получением игры.
     * @param GameResponder      $gameResponder      Формирование ответа JSON:API для игры.
     */
    public function __construct(
        private GetGameHttpAdapter $getGameHttpAdapter,
        private GameResponder $gameResponder,
    ) {
    }

    /**
     * Вернёт игру по filter[secretId].
     *
     * @param Request $request HTTP-запрос.
     *
     * @throws ValidatorException
     */
    #[Route(
        path: '/api/v1/games',
        name: 'api_v1_get_game',
        methods: [Request::METHOD_GET]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $gameWithGuesses = $this->getGameHttpAdapter->execute($request);

        return new JsonResponse(
            data: $this->gameResponder->composeOne(
                $gameWithGuesses->game,
                $gameWithGuesses->guesses,
            ),
            status: Response::HTTP_OK,
            headers: ['Content-Type' => 'application/json'],
        );
    }
}
