<?php

declare(strict_types=1);

namespace App\Infrastructure\KafkaFeature\Http\V1\Controller;

use App\Infrastructure\Common\Http\Middleware\XAuthTokenMiddleware;
use App\Infrastructure\Common\JsonEncoding\JsonEncoder;
use App\Infrastructure\KafkaFeature\KafkaMessage;
use JsonException;
use Kafkiansky\SymfonyMiddleware\Attribute\Middleware;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Middleware([XAuthTokenMiddleware::class])]
class SendToKafkaController extends AbstractController
{
    /**
     * Отправит уведомление в Kafka.
     *
     * @throws ExceptionInterface
     * @throws JsonException
     */
    #[Route(path: '/api/v1/kafka', name: 'api_v1_send_to_kafka', methods: [Request::METHOD_POST])]
    public function __invoke(MessageBusInterface $messageBus, JsonEncoder $jsonEncoder): void
    {
        $content = $jsonEncoder->encode([
            'message' => 'Hello from Wordle Duel Api Symfony!!!'
        ]);
        $message = new KafkaMessage($content);
        $messageBus->dispatch($message);
    }
}
