<?php

declare(strict_types=1);

namespace App\Infrastructure\KafkaFeature;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('kafka')]
readonly class KafkaMessage
{
    public function __construct(
        private string $content,
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
