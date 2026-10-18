<?php

declare(strict_types=1);

namespace App\Infrastructure\SecretFeature\Http\V1\Controller;

use App\Infrastructure\Common\Http\Middleware\XAuthTokenMiddleware;
use App\Infrastructure\Common\Http\V1\Exception\ValidatorException;
use App\Infrastructure\Common\Http\V1\Responder\JsonApiErrorResponder;
use App\Infrastructure\Common\Http\V1\Responder\ValidationExceptionResponder;
use App\Infrastructure\SecretFeature\Http\V1\ACL\Adaptor\CreateSecretHttpAdapter;
use App\Infrastructure\SecretFeature\Http\V1\Responder\SecretResponder;
use Kafkiansky\SymfonyMiddleware\Attribute\Middleware;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Контроллер для сохранения загаданного слова.
 */
#[Middleware([XAuthTokenMiddleware::class])]
final class CreateSecretController extends AbstractController
{
    /**
     * Создаст контроллер.
     *
     * @param LoggerInterface              $logger                       Служба журналирвоания.
     * @param CreateSecretHttpAdapter      $createSecretHttpAdapter      Валидатор HTTP-тела запроса перед сохранением загаданного слова.
     * @param SecretResponder              $secretResponder              Формирование ответа JSON:API для загаданного слова.
     * @param ValidationExceptionResponder $validationExceptionResponder Формирование тела ошибок JSON:API по результатам валидации.
     * @param JsonApiErrorResponder        $jsonApiErrorResponder        Формирование одной ошибки JSON:API (поля status, title, detail).
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly CreateSecretHttpAdapter $createSecretHttpAdapter,
        private readonly SecretResponder $secretResponder,
        private readonly ValidationExceptionResponder $validationExceptionResponder,
        private readonly JsonApiErrorResponder $jsonApiErrorResponder,
    ) {
    }

    /**
     * Сохранит загаданное слово.
     *
     * @param Request $request HTTP-запрос.
     */
    #[Route(
        path: '/api/v1/secrets',
        name: 'api_v1_create_secret',
        methods: [Request::METHOD_POST]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $response = new JsonResponse();

        try {
            $secret = $this->createSecretHttpAdapter->execute($request);
            $response->setStatusCode(Response::HTTP_CREATED);
            $response->setData($this->secretResponder->composeOne($secret));
        } catch (ValidatorException $exception) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setData(['errors' => $this->validationExceptionResponder->compose($exception->violations)]);
        } catch (\Throwable $exception) {
            $this->logger->critical(
                message: 'При создании загаданного слова произошла непредвиденная ошибка.',
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
