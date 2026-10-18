<?php

declare(strict_types=1);

namespace App\Infrastructure\LetterFeature\Doctrine\Entity;

use App\Domain\LetterFeature\Enum\LetterStatus;
use App\Domain\LetterFeature\Model\LetterInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * Сущность буквы догадки.
 */
#[ORM\Table(name: 'letters')]
#[ORM\Entity(repositoryClass: \App\Infrastructure\LetterFeature\Doctrine\Repository\LetterRepository::class)]
#[ORM\Index(name: 'idx_letters_guess_id', columns: ['guess_id'])]
class Letter implements LetterInterface
{
    /**
     * Идентификатор буквы (UUIDv4 на PostgreSQL до 18).
     */
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private Uuid $id;

    /**
     * Идентификатор догадки.
     */
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $guessId;

    /**
     * Номер буквы в слове.
     */
    #[ORM\Column(name: '`index`')]
    private int $index;

    /**
     * Значение буквы.
     */
    #[ORM\Column(length: 1)]
    private string $value;

    /**
     * Статус буквы.
     */
    #[ORM\Column(length: 32, enumType: LetterStatus::class)]
    private LetterStatus $status;

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
     * @param string       $guessId Идентификатор догадки.
     * @param int          $index   Номер буквы в слове.
     * @param string       $value   Значение буквы.
     * @param LetterStatus $status  Статус буквы.
     */
    public function __construct(
        string $guessId,
        int $index,
        string $value,
        LetterStatus $status,
    ) {
        $this->id = new UuidV4();
        $this->guessId = Uuid::fromString($guessId);
        $this->index = $index;
        $this->value = $value;
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
    public function getGuessId(): ?string
    {
        return $this->guessId->toRfc4122();
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
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): ?LetterStatus
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
