<?php

declare(strict_types=1);

namespace App\Infrastructure\GameFeature\Http\V1\Controller;

use App\Domain\GameFeature\Exception\GameCreationConflictException;
use App\Domain\SecretFeature\Exception\SecretNotFoundException;
use App\Infrastructure\Common\Http\Middleware\XAuthTokenMiddleware;
use App\Infrastructure\Common\Http\V1\Exception\ValidatorException;
use App\Infrastructure\Common\Http\V1\Responder\JsonApiErrorResponder;
use App\Infrastructure\Common\Http\V1\Responder\ValidationExceptionResponder;
use App\Infrastructure\GameFeature\Http\V1\ACL\Adaptor\CreateGameHttpAdapter;
use App\Infrastructure\GameFeature\Http\V1\Responder\GameResponder;
use Kafkiansky\SymfonyMiddleware\Attribute\Middleware;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Контроллер для создания игры.
 */
#[Middleware([XAuthTokenMiddleware::class])]
final class CreateGameController extends AbstractController
{
    /**
     * Создаст контроллер.
     *
     * @param LoggerInterface              $logger                       Служба журналирования.
     * @param CreateGameHttpAdapter        $createGameHttpAdapter        Адаптер HTTP-тела запроса перед созданием игры.
     * @param GameResponder                $gameResponder                Формирование ответа JSON:API для игры.
     * @param ValidationExceptionResponder $validationExceptionResponder Формирование тела ошибок JSON:API по результатам валидации.
     * @param JsonApiErrorResponder        $jsonApiErrorResponder        Формирование одной ошибки JSON:API.
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly CreateGameHttpAdapter $createGameHttpAdapter,
        private readonly GameResponder $gameResponder,
        private readonly ValidationExceptionResponder $validationExceptionResponder,
        private readonly JsonApiErrorResponder $jsonApiErrorResponder,
    ) {
    }

    /**
     * Создаст игру.
     *
     * @param Request $request HTTP-запрос.
     */
    #[Route(path: '/api/v1/games', name: 'api_v1_create_game', methods: [Request::METHOD_POST])]
    public function __invoke(Request $request): JsonResponse
    {
        $response = new JsonResponse();

        try {
            $game = $this->createGameHttpAdapter->execute($request);
            $response->setStatusCode(Response::HTTP_CREATED);
            $response->setData($this->gameResponder->composeOne($game));
        } catch (ValidatorException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(['errors' => $this->validationExceptionResponder->compose($exception->violations)]);
        } catch (SecretNotFoundException $exception) {
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
        } catch (GameCreationConflictException $exception) {
            $response->setStatusCode(Response::HTTP_CONFLICT);
            $response->setData([
                'errors' => [
                    $this->jsonApiErrorResponder->compose(
                        status: (string) Response::HTTP_CONFLICT,
                        title: 'Конфликт.',
                        detail: $exception->getMessage(),
                    ),
                ],
            ]);
        } catch (\Throwable $exception) {
            $this->logger->critical(
                message: 'При создании игры произошла непредвиденная ошибка.',
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
