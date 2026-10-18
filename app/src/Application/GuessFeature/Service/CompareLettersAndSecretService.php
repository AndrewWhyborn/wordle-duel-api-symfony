<?php

declare(strict_types=1);

namespace App\Application\GuessFeature\Service;

use App\Domain\LetterFeature\Enum\LetterStatus;
use App\Domain\SecretFeature\Model\SecretInterface;

/**
 * Служба сравнения введённых букв с загаданным словом.
 */
readonly class CompareLettersAndSecretService
{
    /**
     * Сравнит буквы догадки с буквами загаданного слова.
     *
     * Два прохода: сначала SUCCESS (точное совпадение позиции),
     * затем NEAR/FAR с учётом уже «съеденных» букв secret.
     *
     * @param SecretInterface $secret  Загаданное слово.
     * @param list<string>    $letters Введённые буквы.
     *
     * @return list<LetterStatus>
     */
    public function execute(SecretInterface $secret, array $letters): array
    {
        $secretLetters = \mb_str_split(\mb_strtoupper($secret->getValue()));
        $inputLetters = \array_map(
            static fn (string $letter): string => \mb_strtoupper($letter),
            $letters,
        );

        $length = \count($inputLetters);
        $statuses = \array_fill(0, $length, LetterStatus::FAR);
        $checked = \array_fill(0, \count($secretLetters), false);

        foreach ($inputLetters as $index => $letter) {
            if (($secretLetters[$index] ?? null) === $letter) {
                $statuses[$index] = LetterStatus::SUCCESS;
                $checked[$index] = true;
            }
        }

        foreach ($inputLetters as $index => $letter) {
            if ($statuses[$index] === LetterStatus::SUCCESS) {
                continue;
            }

            foreach ($secretLetters as $secretIndex => $secretLetter) {
                if ($checked[$secretIndex] === true) {
                    continue;
                }

                if ($secretLetter === $letter) {
                    $statuses[$index] = LetterStatus::NEAR;
                    $checked[$secretIndex] = true;
                    break;
                }
            }
        }

        return $statuses;
    }
}
