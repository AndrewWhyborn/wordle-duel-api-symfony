<?php

declare(strict_types=1);

namespace App\Infrastructure\SecretFeature\Doctrine\Entity;

use App\Domain\SecretFeature\Model\SecretInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Сущность загаданного слова.
 */
#[ORM\Table(name: 'secrets')]
#[ORM\Entity(repositoryClass: \App\Infrastructure\SecretFeature\Doctrine\Repository\SecretRepository::class)]
class Secret implements SecretInterface
{
    /**
     * Идентификатор записи.
     */
    #[ORM\Id]
    #[ORM\Column(length: 32)]
    private string $id;

    /**
     * Значение загаданного слова.
     */
    #[ORM\Column(length: 10)]
    private string $value;

    /**
     * Является ли слово реальным (следует ли проверять в словаре).
     */
    #[ORM\Column]
    private bool $isRealWord;

    /**
     * Разрешено ли переиграть.
     */
    #[ORM\Column]
    private bool $allowRetry;

    /**
     * Дата создания.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * Конструктор сущности.
     *
     * @param string $id         Уникальный идентификатор записи.
     * @param string $value      Значение загаданного слова.
     * @param bool   $isRealWord Является ли слово реальным (следует ли проверять в словаре).
     * @param bool   $allowRetry Разрешено ли переиграть.
     */
    public function __construct(
        string $id,
        string $value,
        bool $isRealWord,
        bool $allowRetry,
    ) {
        $this->id = $id;
        $this->value = $value;
        $this->isRealWord = $isRealWord;
        $this->allowRetry = $allowRetry;
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function isRealWord(): ?bool
    {
        return $this->isRealWord;
    }

    /**
     * @inheritDoc
     */
    public function isAllowRetry(): ?bool
    {
        return $this->allowRetry;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}
