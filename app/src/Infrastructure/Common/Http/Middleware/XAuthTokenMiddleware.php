<?php

declare(strict_types=1);

namespace App\Infrastructure\Common\Http\Middleware;

use App\Infrastructure\Common\Http\V1\Responder\JsonApiErrorResponder;
use Kafkiansky\SymfonyMiddleware\Psr\PsrResponseTransformer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * PSR-15 middleware проверки заголовка X-Auth-Token.
 */
final readonly class XAuthTokenMiddleware implements MiddlewareInterface
{
    /**
     * Создаст middleware.
     *
     * @param string                  $xAuthToken             Ожидаемое значение X-Auth-Token из окружения.
     * @param JsonApiErrorResponder   $jsonApiErrorResponder  Формирование ошибки JSON:API.
     * @param PsrResponseTransformer  $psrResponseTransformer Преобразование Symfony Response в PSR-7.
     */
    public function __construct(
        private string                 $xAuthToken,
        private JsonApiErrorResponder  $jsonApiErrorResponder,
        private PsrResponseTransformer $psrResponseTransformer,
    ) {
    }

    /**
     * Проверит заголовок X-Auth-Token и вернёт 401 при отсутствии или неверном значении.
     *
     * @param ServerRequestInterface  $request PSR-7 запрос.
     * @param RequestHandlerInterface $handler Следующий обработчик в цепочке.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeaderLine('X-Auth-Token');
        if ($token !== '' && \hash_equals($this->xAuthToken, $token)) {
            return $handler->handle($request);
        }

        $error = $this->jsonApiErrorResponder->compose(
            status: (string) Response::HTTP_UNAUTHORIZED,
            title: 'Не авторизован',
            detail: 'Неверный или отсутствующий заголовок X-Auth-Token.',
        );

        return $this->psrResponseTransformer->toPsrResponse(new JsonResponse(
            data: ['errors' => [$error]],
            status: Response::HTTP_UNAUTHORIZED,
            headers: ['Content-Type' => 'application/json'],
        ));
    }
}
