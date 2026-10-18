<?php

declare(strict_types=1);

namespace App\Infrastructure\GameFeature\Http\V1\Controller;

use App\Domain\GameFeature\Exception\GameNotFoundException;
use App\Domain\SecretFeature\Exception\SecretNotFoundException;
use App\Infrastructure\Common\Http\Middleware\XAuthTokenMiddleware;
use App\Infrastructure\Common\Http\V1\Exception\ValidatorException;
use App\Infrastructure\Common\Http\V1\Responder\JsonApiErrorResponder;
use App\Infrastructure\Common\Http\V1\Responder\ValidationExceptionResponder;
use App\Infrastructure\GameFeature\Http\V1\ACL\Adaptor\GetGameHttpAdapter;
use App\Infrastructure\GameFeature\Http\V1\Responder\GameResponder;
use Kafkiansky\SymfonyMiddleware\Attribute\Middleware;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Контроллер для получения игры.
 */
#[Middleware([XAuthTokenMiddleware::class])]
final class GetGameController extends AbstractController
{
    /**
     * Создаст контроллер.
     *
     * @param LoggerInterface              $logger                       Служба журналирования.
     * @param GetGameHttpAdapter           $getGameHttpAdapter           Адаптер query-параметров перед получением игры.
     * @param GameResponder                $gameResponder                Формирование ответа JSON:API для игры.
     * @param ValidationExceptionResponder $validationExceptionResponder Формирование тела ошибок JSON:API по результатам валидации.
     * @param JsonApiErrorResponder        $jsonApiErrorResponder        Формирование одной ошибки JSON:API (поля status, title, detail).
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly GetGameHttpAdapter $getGameHttpAdapter,
        private readonly GameResponder $gameResponder,
        private readonly ValidationExceptionResponder $validationExceptionResponder,
        private readonly JsonApiErrorResponder $jsonApiErrorResponder,
    ) {
    }

    /**
     * Вернёт игру по filter[secretId].
     *
     * @param Request $request HTTP-запрос.
     */
    #[Route(path: '/api/v1/games', name: 'api_v1_get_game', methods: [Request::METHOD_GET])]
    public function __invoke(Request $request): JsonResponse
    {
        $response = new JsonResponse();

        try {
            $gameWithGuesses = $this->getGameHttpAdapter->execute($request);
            $response->setStatusCode(Response::HTTP_OK);
            $response->setData(
                $this->gameResponder->composeOne(
                    $gameWithGuesses->game,
                    $gameWithGuesses->guesses,
                ),
            );
        } catch (ValidatorException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(['errors' => $this->validationExceptionResponder->compose($exception->violations)]);
        } catch (SecretNotFoundException|GameNotFoundException $exception) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->setData([
                'errors' => [
                    $this->jsonApiErrorResponder->compose(
                        status: (string) Response::HTTP_NOT_FOUND,
                        title: 'Не найдено',
                        detail: $exception->getMessage(),
                    ),
                ],
            ]);
        } catch (\Throwable $exception) {
            $this->logger->critical(
                message: 'При получении игры произошла непредвиденная ошибка.',
                context: [
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                ],
            );

            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setData([
                'errors' => [
                    $this->jsonApiErrorResponder->compose(
                        status: (string) Response::HTTP_INTERNAL_SERVER_ERROR,
                        title: 'Ошибка сервера',
                        detail: $exception->getMessage(),
                    ),
                ],
            ]);
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
