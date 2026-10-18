<?php

declare(strict_types=1);

namespace App\Infrastructure\GameFeature\Doctrine\Entity;

use App\Domain\GameFeature\Enum\GameStatus;
use App\Domain\GameFeature\Exception\InvalidGameStatusTransitionException;
use App\Domain\GameFeature\Model\GameInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

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
     * Идентификатор игры (UUIDv4 на PostgreSQL до 18).
     */
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private Uuid $id;

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
        $this->id = new UuidV4();
        $this->secretId = $secretId;
        $this->ip = $ip;
        $this->status = $status;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?string
    {
        return $this->id->toRfc4122();
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
    public function setStatus(GameStatus $status): void
    {
        if ($this->status !== GameStatus::IN_PROGRESS) {
            throw new InvalidGameStatusTransitionException();
        }

        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();
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
