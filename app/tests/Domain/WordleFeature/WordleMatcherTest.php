<?php

declare(strict_types=1);

namespace App\Tests\Domain\WordleFeature;

use App\Domain\LetterFeature\Enum\LetterStatus;
use App\Domain\WordleFeature\WordleMatcher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Тесты сравнения букв догадки с загаданным словом.
 */
final class WordleMatcherTest extends TestCase
{
    private WordleMatcher $matcher;

    protected function setUp(): void
    {
        $this->matcher = new WordleMatcher();
    }

    /**
     * @param list<string>       $secretLetters
     * @param list<string>       $guessLetters
     * @param list<LetterStatus> $expected
     */
    #[DataProvider('comparisonCasesProvider')]
    public function testCompareLetters(array $secretLetters, array $guessLetters, array $expected): void
    {
        $this->assertSame($expected, $this->matcher->execute($secretLetters, $guessLetters));
    }

    /**
     * @return iterable<string, array{list<string>, list<string>, list<LetterStatus>}>
     */
    public static function comparisonCasesProvider(): iterable
    {
        yield 'агент vs агент — все SUCCESS' => [
            ['а', 'г', 'е', 'н', 'т'],
            ['а', 'г', 'е', 'н', 'т'],
            [
                LetterStatus::SUCCESS,
                LetterStatus::SUCCESS,
                LetterStatus::SUCCESS,
                LetterStatus::SUCCESS,
                LetterStatus::SUCCESS,
            ],
        ];

        yield 'агент vs палка — только первая а NEAR' => [
            ['п', 'а', 'л', 'к', 'а'],
            ['а', 'г', 'е', 'н', 'т'],
            [
                LetterStatus::NEAR,
                LetterStatus::FAR,
                LetterStatus::FAR,
                LetterStatus::FAR,
                LetterStatus::FAR,
            ],
        ];

        yield 'галка vs палка — FAR и четыре SUCCESS' => [
            ['п', 'а', 'л', 'к', 'а'],
            ['г', 'а', 'л', 'к', 'а'],
            [
                LetterStatus::FAR,
                LetterStatus::SUCCESS,
                LetterStatus::SUCCESS,
                LetterStatus::SUCCESS,
                LetterStatus::SUCCESS,
            ],
        ];

        yield 'калач vs палка — к NEAR, а SUCCESS, л SUCCESS, а NEAR, ч FAR' => [
            ['п', 'а', 'л', 'к', 'а'],
            ['к', 'а', 'л', 'а', 'ч'],
            [
                LetterStatus::NEAR,
                LetterStatus::SUCCESS,
                LetterStatus::SUCCESS,
                LetterStatus::NEAR,
                LetterStatus::FAR,
            ],
        ];

        yield 'альфа vs агент — вторая а FAR после SUCCESS' => [
            ['а', 'г', 'е', 'н', 'т'],
            ['а', 'л', 'ь', 'ф', 'а'],
            [
                LetterStatus::SUCCESS,
                LetterStatus::FAR,
                LetterStatus::FAR,
                LetterStatus::FAR,
                LetterStatus::FAR,
            ],
        ];
    }
}
