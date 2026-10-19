<?php

declare(strict_types=1);

namespace App\Infrastructure\GuessFeature\Http\V1\Controller;

use App\Infrastructure\Common\Http\Middleware\XAuthTokenMiddleware;
use App\Infrastructure\Common\Http\V1\Exception\ValidatorException;
use App\Infrastructure\GuessFeature\Http\V1\ACL\Adaptor\CreateGuessHttpAdapter;
use App\Infrastructure\GuessFeature\Http\V1\Responder\GuessResponder;
use Kafkiansky\SymfonyMiddleware\Attribute\Middleware;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Контроллер для создания догадки.
 */
#[Middleware([XAuthTokenMiddleware::class])]
final readonly class CreateGuessController
{
    /**
     * Создаст контроллер.
     *
     * @param CreateGuessHttpAdapter $createGuessHttpAdapter Адаптер HTTP-тела запроса перед созданием догадки.
     * @param GuessResponder         $guessResponder         Формирование ответа JSON:API для догадки.
     */
    public function __construct(
        private CreateGuessHttpAdapter $createGuessHttpAdapter,
        private GuessResponder $guessResponder,
    ) {
    }

    /**
     * Создаст догадку.
     *
     * @param Request $request HTTP-запрос.
     *
     * @throws ValidatorException
     */
    #[Route(
        path: '/api/v1/guesses',
        name: 'api_v1_create_guess',
        methods: [Request::METHOD_POST]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $guessWithLetters = $this->createGuessHttpAdapter->execute($request);

        return new JsonResponse(
            data: $this->guessResponder->composeOne(
                $guessWithLetters->guess,
                $guessWithLetters->letters,
            ),
            status: Response::HTTP_CREATED,
            headers: ['Content-Type' => 'application/json'],
        );
    }
}
