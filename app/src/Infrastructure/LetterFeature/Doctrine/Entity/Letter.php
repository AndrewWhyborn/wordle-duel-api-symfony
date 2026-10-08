<?php

declare(strict_types=1);

namespace App\Infrastructure\LetterFeature\Doctrine\Entity;

use App\Domain\LetterFeature\Enum\LetterStatus;
use App\Domain\LetterFeature\Model\LetterInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Сущность буквы догадки.
 */
#[ORM\Table(name: 'letters')]
#[ORM\Entity(repositoryClass: \App\Infrastructure\LetterFeature\Doctrine\Repository\LetterRepository::class)]
#[ORM\Index(name: 'idx_letters_guess_id', columns: ['guess_id'])]
class Letter implements LetterInterface
{
    /**
     * Идентификатор буквы.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Идентификатор догадки.
     */
    #[ORM\Column]
    private int $guessId;

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
     * @param int          $guessId Идентификатор догадки.
     * @param int          $index   Номер буквы в слове.
     * @param string       $value   Значение буквы.
     * @param LetterStatus $status  Статус буквы.
     */
    public function __construct(
        int $guessId,
        int $index,
        string $value,
        LetterStatus $status,
    ) {
        $this->guessId = $guessId;
        $this->index = $index;
        $this->value = $value;
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
    public function getGuessId(): ?int
    {
        return $this->guessId;
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
