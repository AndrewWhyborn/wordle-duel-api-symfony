<?php

declare(strict_types=1);

namespace App\Domain\WordleFeature;

use App\Domain\LetterFeature\Enum\LetterStatus;

/**
 * Сравнение догадки с загаданным словом (правила Wordle).
 */
final readonly class WordleMatcher
{
    /**
     * Сравнит буквы догадки с буквами загаданного слова.
     *
     * @param list<string> $secretLetters Буквы загаданного слова.
     * @param list<string> $guessLetters  Введённые буквы догадки.
     *
     * @return list<LetterStatus>
     */
    public function execute(array $secretLetters, array $guessLetters): array
    {
        $secretLetters = \array_map(
            static fn (string $letter): string => \mb_strtoupper($letter),
            $secretLetters,
        );
        $guessLetters = \array_map(
            static fn (string $letter): string => \mb_strtoupper($letter),
            $guessLetters,
        );

        return $this->performExecute($secretLetters, $guessLetters);
    }

    /**
     * Алгоритм сравнения (буквы уже в верхнем регистре).
     *
     * Два прохода: сначала SUCCESS (точное совпадение позиции),
     * затем NEAR/FAR с учётом уже «съеденных» букв secret.
     *
     * @param list<string> $secretLetters Буквы загаданного слова.
     * @param list<string> $guessLetters  Введённые буквы догадки.
     *
     * @return list<LetterStatus>
     */
    private function performExecute(array $secretLetters, array $guessLetters): array
    {
        $length = \count($guessLetters);
        $statuses = \array_fill(0, $length, LetterStatus::FAR);
        $checked = \array_fill(0, \count($secretLetters), false);

        foreach ($guessLetters as $index => $letter) {
            if (($secretLetters[$index] ?? null) === $letter) {
                $statuses[$index] = LetterStatus::SUCCESS;
                $checked[$index] = true;
            }
        }

        foreach ($guessLetters as $index => $letter) {
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
