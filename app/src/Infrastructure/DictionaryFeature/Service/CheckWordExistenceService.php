<?php

declare(strict_types=1);

namespace App\Infrastructure\DictionaryFeature\Service;

use App\Domain\DictionaryFeature\Service\CheckWordExistenceServiceInterface;
use App\Infrastructure\DictionaryFeature\Yandex\Http\LookupHttpService;
use Psr\Log\LoggerInterface;

/**
 * Служба проверки существования слова в Яндекс Словаре.
 */
readonly class CheckWordExistenceService implements CheckWordExistenceServiceInterface
{
    /**
     * Существительное.
     */
    private const NOUN = 'noun';

    /**
     * Создаст службу.
     *
     * @param LoggerInterface   $logger            Служба журналирования.
     * @param LookupHttpService $lookupHttpService Служба для поиска слова в Яндекс Словаре.
     */
    public function __construct(
        private LoggerInterface $logger,
        private LookupHttpService $lookupHttpService,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(string $word): bool
    {
        try {
            $result = $this->lookupHttpService->execute(text: $word);

            if (
                isset($result[0]['pos'])
                && $result[0]['pos'] === self::NOUN
            ) {
                return true;
            }
        } catch (\Throwable $exception) {
            $this->logger->error(
                message: 'Во время проверки слова в словаре произошла ошибка.',
                context: [
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                ]
            );

            return false;
        }

        return false;
    }
}
