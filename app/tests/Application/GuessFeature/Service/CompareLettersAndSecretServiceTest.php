<?php

declare(strict_types=1);

namespace App\Tests\Application\GuessFeature\Service;

use App\Application\GuessFeature\Service\CompareLettersAndSecretService;
use App\Domain\LetterFeature\Enum\LetterStatus;
use App\Domain\SecretFeature\Model\SecretInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Тесты сравнения букв догадки с загаданным словом.
 */
final class CompareLettersAndSecretServiceTest extends TestCase
{
    private CompareLettersAndSecretService $service;

    protected function setUp(): void
    {
        $this->service = new CompareLettersAndSecretService();
    }

    /**
     * @param list<string>       $letters
     * @param list<LetterStatus> $expected
     */
    #[DataProvider('comparisonCasesProvider')]
    public function testCompareLetters(string $secretValue, array $letters, array $expected): void
    {
        $secret = $this->createStub(SecretInterface::class);
        $secret->method('getValue')->willReturn($secretValue);

        $this->assertSame($expected, $this->service->execute($secret, $letters));
    }

    /**
     * @return iterable<string, array{string, list<string>, list<LetterStatus>}>
     */
    public static function comparisonCasesProvider(): iterable
    {
        yield 'агент vs агент — все SUCCESS' => [
            'агент',
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
            'палка',
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
            'палка',
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
            'палка',
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
            'агент',
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
