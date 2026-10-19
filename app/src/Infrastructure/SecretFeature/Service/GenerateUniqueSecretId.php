<?php

declare(strict_types=1);

namespace App\Infrastructure\SecretFeature\Service;

use App\Domain\SecretFeature\Exception\SecretIdGenerationFailedException;
use App\Domain\SecretFeature\Repository\SecretRepositoryInterface;
use App\Domain\SecretFeature\Service\GenerateUniqueSecretIdInterface;
use App\Domain\StringGeneratorFeature\RandomStringGeneratorInterface;

/**
 * Генерация уникального идентификатора секрета.
 */
readonly class GenerateUniqueSecretId implements GenerateUniqueSecretIdInterface
{
    /**
     * Длина идентификатора.
     */
    private const ID_LENGTH = 6;

    /**
     * Максимальное число попыток подобрать свободный id.
     */
    private const MAX_ATTEMPTS = 10;

    /**
     * Создаст службу.
     *
     * @param RandomStringGeneratorInterface $randomStringGenerator Генератор строк.
     * @param SecretRepositoryInterface      $secretRepository      Репозиторий секретов.
     */
    public function __construct(
        private RandomStringGeneratorInterface $randomStringGenerator,
        private SecretRepositoryInterface $secretRepository,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(): string
    {
        $attempt = 0;

        do {
            if ($attempt >= self::MAX_ATTEMPTS) {
                throw new SecretIdGenerationFailedException(
                    'Превышено максимальное кол-во попыток сгенерировать уникальный идентификатор.',
                );
            }

            $id = $this->randomStringGenerator->generate(self::ID_LENGTH);
            $attempt++;
        } while ($this->secretRepository->existsById($id));

        return $id;
    }
}
