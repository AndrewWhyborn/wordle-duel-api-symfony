<?php

declare(strict_types=1);

namespace App\Infrastructure\GameFeature\Doctrine\Entity;

use App\Domain\GameFeature\Enum\GameStatus;
use App\Domain\GameFeature\Model\GameInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Сущность игры.
 */
#[ORM\Table(name: 'games')]
#[ORM\Entity(repositoryClass: \App\Infrastructure\GameFeature\Doctrine\Repository\GameRepository::class)]
#[ORM\Index(name: 'idx_games_secret_id', columns: ['secret_id'])]
#[ORM\Index(name: 'idx_games_ip', columns: ['ip'])]
class Game implements GameInterface
{
    /**
     * Идентификатор игры.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Идентификатор загаданного слова.
     */
    #[ORM\Column(length: 32)]
    private string $secretId;

    /**
     * IP игрока.
     */
    #[ORM\Column(length: 45)]
    private string $ip;

    /**
     * Статус игры.
     */
    #[ORM\Column(length: 32, enumType: GameStatus::class)]
    private GameStatus $status;

    /**
     * Дата создания.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * Дата обновления.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    /**
     * Конструктор сущности.
     *
     * @param string     $secretId Идентификатор загаданного слова.
     * @param string     $ip       IP игрока.
     * @param GameStatus $status   Статус игры.
     */
    public function __construct(
        string $secretId,
        string $ip,
        GameStatus $status = GameStatus::IN_PROGRESS,
    ) {
        $this->secretId = $secretId;
        $this->ip = $ip;
        $this->status = $status;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getSecretId(): ?string
    {
        return $this->secretId;
    }

    /**
     * @inheritDoc
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): ?GameStatus
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
