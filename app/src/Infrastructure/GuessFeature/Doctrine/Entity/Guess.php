<?php

declare(strict_types=1);

namespace App\Infrastructure\GuessFeature\Doctrine\Entity;

use App\Domain\GuessFeature\Model\GuessInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Сущность догадки.
 */
#[ORM\Table(name: 'guesses')]
#[ORM\Entity(repositoryClass: \App\Infrastructure\GuessFeature\Doctrine\Repository\GuessRepository::class)]
#[ORM\Index(name: 'idx_guesses_game_id', columns: ['game_id'])]
class Guess implements GuessInterface
{
    /**
     * Идентификатор догадки.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Идентификатор игры.
     */
    #[ORM\Column]
    private int $gameId;

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
     * @param int $gameId Идентификатор игры.
     * @param int $index  Номер догадки в игре.
     */
    public function __construct(int $gameId, int $index)
    {
        $this->gameId = $gameId;
        $this->index = $index;
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
    public function getGameId(): ?int
    {
        return $this->gameId;
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
