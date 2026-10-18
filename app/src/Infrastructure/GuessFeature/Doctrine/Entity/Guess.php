<?php

declare(strict_types=1);

namespace App\Infrastructure\GuessFeature\Doctrine\Entity;

use App\Domain\GuessFeature\Model\GuessInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Сущность догадки.
 */
#[ORM\Table(name: 'guesses')]
#[ORM\Entity(repositoryClass: \App\Infrastructure\GuessFeature\Doctrine\Repository\GuessRepository::class)]
#[ORM\Index(name: 'idx_guesses_game_id', columns: ['game_id'])]
class Guess implements GuessInterface
{
    /**
     * Идентификатор догадки (UUIDv4 на PostgreSQL до 18).
     */
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private Uuid $id;

    /**
     * Идентификатор игры.
     */
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $gameId;

    /**
     * Номер догадки в игре.
     */
    #[ORM\Column(name: '`index`')]
    private int $index;

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
     * @param string $gameId Идентификатор игры.
     * @param int    $index  Номер догадки в игре.
     */
    public function __construct(string $gameId, int $index)
    {
        $this->id = new UuidV4();
        $this->gameId = Uuid::fromString($gameId);
        $this->index = $index;
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
    public function getGameId(): ?string
    {
        return $this->gameId->toRfc4122();
    }

    /**
     * @inheritDoc
     */
    public function getIndex(): ?int
    {
        return $this->index;
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
