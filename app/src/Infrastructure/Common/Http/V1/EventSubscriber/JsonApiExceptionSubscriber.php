<?php

declare(strict_types=1);

namespace App\Infrastructure\Common\Http\V1\EventSubscriber;

use App\Infrastructure\Common\Http\V1\Exception\JsonApiExceptionMapper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Преобразует исключения на /api в JSON:API-ответы.
 */
final readonly class JsonApiExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * Создаст subscriber.
     *
     * @param JsonApiExceptionMapper $jsonApiExceptionMapper Mapper исключений в JSON:API.
     */
    public function __construct(
        private JsonApiExceptionMapper $jsonApiExceptionMapper,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 10],
        ];
    }

    /**
     * Обработает исключение для API-маршрутов.
     *
     * @param ExceptionEvent $event Событие исключения ядра.
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $path = $event->getRequest()->getPathInfo();

        if (!\str_starts_with($path, '/api')) {
            return;
        }

        // Swagger UI / OpenAPI spec — оставляем стандартную обработку Symfony/Nelmio.
        if (\str_starts_with($path, '/api/doc')) {
            return;
        }

        $event->setResponse($this->jsonApiExceptionMapper->map($event->getThrowable()));
    }
}
