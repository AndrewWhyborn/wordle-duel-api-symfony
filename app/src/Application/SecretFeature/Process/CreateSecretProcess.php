<?php

declare(strict_types=1);

namespace App\Application\SecretFeature\Process;

use App\Application\TokenFeature\Service\GenerateTokenService;
use App\Domain\SecretFeature\Factory\SecretFactoryInterface;
use App\Domain\SecretFeature\Model\SecretInterface;
use App\Domain\SecretFeature\Storage\CreateSecretStorageInterface;

/**
 * Процесс сохранения загаданного слова.
 */
readonly class CreateSecretProcess
{
    /**
     * Название таблицы загаданных слов.
     */
    public const SECRETS_TABLE = 'secrets';

    /**
     * Создаст процесс.
     *
     * @param SecretFactoryInterface       $secretFactory        Фабрика для создания сущности загаданного слова.
     * @param CreateSecretStorageInterface $createSecretStorage  Служба сохранения загаданного слова.
     * @param GenerateTokenService         $generateTokenService Служба для генерации уникального идентификатора.
     */
    public function __construct(
        private SecretFactoryInterface $secretFactory,
        private CreateSecretStorageInterface $createSecretStorage,
        private GenerateTokenService $generateTokenService,
    ) {
    }

    /**
     * Сохранит загаданное слово.
     *
     * @param string $value      Значение загаданного слова.
     * @param bool   $isRealWord Является ли слово реальным (следует ли проверять в словаре).
     * @param bool   $allowRetry Разрешено ли переиграть.
     *
     * @return SecretInterface
     *
     * @throws \Exception
     */
    public function execute(
        string $value,
        bool $isRealWord = true,
        bool $allowRetry = false,
    ): SecretInterface {
        $id = $this->generateTokenService->execute(
            tableName: self::SECRETS_TABLE,
            field: 'id',
        );

        $secret = $this->secretFactory->create($id, $value, $isRealWord, $allowRetry);

        return $this->createSecretStorage->save($secret);
    }
}
