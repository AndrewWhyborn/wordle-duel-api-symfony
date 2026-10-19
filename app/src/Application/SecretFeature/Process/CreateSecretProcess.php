<?php

declare(strict_types=1);

namespace App\Application\SecretFeature\Process;

use App\Domain\SecretFeature\Exception\SecretIdGenerationFailedException;
use App\Domain\SecretFeature\Factory\SecretFactoryInterface;
use App\Domain\SecretFeature\Model\SecretInterface;
use App\Domain\SecretFeature\Service\GenerateUniqueSecretIdInterface;
use App\Domain\SecretFeature\Storage\CreateSecretStorageInterface;

/**
 * Процесс сохранения загаданного слова.
 */
readonly class CreateSecretProcess
{
    /**
     * Создаст процесс.
     *
     * @param SecretFactoryInterface         $secretFactory          Фабрика для создания сущности загаданного слова.
     * @param CreateSecretStorageInterface   $createSecretStorage    Служба сохранения загаданного слова.
     * @param GenerateUniqueSecretIdInterface $generateUniqueSecretId Генерация уникального идентификатора.
     */
    public function __construct(
        private SecretFactoryInterface $secretFactory,
        private CreateSecretStorageInterface $createSecretStorage,
        private GenerateUniqueSecretIdInterface $generateUniqueSecretId,
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
     * @throws SecretIdGenerationFailedException
     */
    public function execute(
        string $value,
        bool $isRealWord = true,
        bool $allowRetry = false,
    ): SecretInterface {
        $id = $this->generateUniqueSecretId->execute();
        $secret = $this->secretFactory->create($id, $value, $isRealWord, $allowRetry);

        return $this->createSecretStorage->save($secret);
    }
}
