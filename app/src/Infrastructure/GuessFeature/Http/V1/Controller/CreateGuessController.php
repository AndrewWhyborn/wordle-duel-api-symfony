<?php

declare(strict_types=1);

namespace App\Infrastructure\GuessFeature\Http\V1\Controller;

use App\Domain\GameFeature\Exception\GameNotFoundException;
use App\Infrastructure\Common\Http\Middleware\XAuthTokenMiddleware;
use App\Infrastructure\Common\Http\V1\Exception\ValidatorException;
use App\Infrastructure\Common\Http\V1\Responder\JsonApiErrorResponder;
use App\Infrastructure\Common\Http\V1\Responder\ValidationExceptionResponder;
use App\Infrastructure\GuessFeature\Http\V1\ACL\Adaptor\CreateGuessHttpAdapter;
use App\Infrastructure\GuessFeature\Http\V1\Responder\GuessResponder;
use Kafkiansky\SymfonyMiddleware\Attribute\Middleware;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Контроллер для создания догадки.
 */
#[Middleware([XAuthTokenMiddleware::class])]
final class CreateGuessController extends AbstractController
{
    /**
     * Создаст контроллер.
     *
     * @param LoggerInterface              $logger                       Служба журналирования.
     * @param CreateGuessHttpAdapter       $createGuessHttpAdapter       Адаптер HTTP-тела запроса перед созданием догадки.
     * @param GuessResponder               $guessResponder               Формирование ответа JSON:API для догадки.
     * @param ValidationExceptionResponder $validationExceptionResponder Формирование тела ошибок JSON:API по результатам валидации.
     * @param JsonApiErrorResponder        $jsonApiErrorResponder        Формирование одной ошибки JSON:API (поля status, title, detail).
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly CreateGuessHttpAdapter $createGuessHttpAdapter,
        private readonly GuessResponder $guessResponder,
        private readonly ValidationExceptionResponder $validationExceptionResponder,
        private readonly JsonApiErrorResponder $jsonApiErrorResponder,
    ) {
    }

    /**
     * Создаст догадку.
     *
     * @param Request $request HTTP-запрос.
     */
    #[Route(
        path: '/api/v1/guesses',
        name: 'api_v1_create_guess',
        methods: [Request::METHOD_POST]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $response = new JsonResponse();

        try {
            $guessWithLetters = $this->createGuessHttpAdapter->execute($request);
            $response->setStatusCode(Response::HTTP_CREATED);
            $response->setData(
                $this->guessResponder->composeOne(
                    $guessWithLetters->guess,
                    $guessWithLetters->letters,
                ),
            );
        } catch (ValidatorException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(['errors' => $this->validationExceptionResponder->compose($exception->violations)]);
        } catch (GameNotFoundException $exception) {
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
                message: 'При создании догадки произошла непредвиденная ошибка.',
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
