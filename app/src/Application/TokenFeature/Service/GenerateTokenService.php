<?php

declare(strict_types=1);

namespace App\Application\TokenFeature\Service;

use App\Domain\StringGeneratorFeature\RandomStringGeneratorInterface;
use App\Domain\TokenFeature\Service\CheckAvailableTokenServiceInterface;

/**
 * Служба для генерации уникального токена.
 */
readonly class GenerateTokenService
{
    /**
     * Максимальное кол-во попыток на создание уникального токена.
     */
    private const MAX_ATTEMPTS = 10;

    /**
     * Создаст службу.
     *
     * @param RandomStringGeneratorInterface      $randomStringGenerator      Генератор строк.
     * @param CheckAvailableTokenServiceInterface $checkAvailableTokenService Служба для проверки наличия токена в таблице.
     */
    public function __construct(
        private RandomStringGeneratorInterface $randomStringGenerator,
        private CheckAvailableTokenServiceInterface $checkAvailableTokenService,
    ) {
    }

    /**
     * Сгенерирует уникальный токен для атрибута таблицы.
     *
     * @param string $tableName Название таблицы.
     * @param int    $length    Длина токена.
     * @param string $field     Атрибут таблицы в котором хранится уникальный токен.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function execute(
        string $tableName,
        int $length = 6,
        string $field = 'token',
    ): string {
        if ($length < 4) {
            throw new \Exception(
                \sprintf(
                    'Токен длиной в %d символа не сможет обеспечить достаточную уникальность.',
                    $length,
                ),
            );
        }

        $attempt = 0;

        do {
            if ($attempt > self::MAX_ATTEMPTS) {
                throw new \Exception('Превышено максимальное кол-во попыток сгенерировать уникальный токен.');
            }

            $token = $this->randomStringGenerator->generate($length);
            $attempt++;

            $tokenExist = $this->checkAvailableTokenService->execute($tableName, $token, $field);
        } while ($tokenExist);

        return $token;
    }
}
