<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Service\WordGameService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class WordGameServiceTest extends TestCase
{
    private WordGameService $svc;

    protected function setUp(): void
    {
        $this->svc = new WordGameService();
    }

    public static function provideNormalize(): array
    {
        return [
            ['Level', 'level'],
            ['He!!o-World', 'heoworld'],
            ['  CIVIC  ', 'civic'],
            ['123abc', 'abc'],
            ['', ''],
            ['@@@', ''],
        ];
    }

    #[DataProvider('provideNormalize')]
    public function testNormalize(string $in, string $expected): void
    {
        self::assertSame($expected, $this->svc->normalize($in));
    }

    public static function providePalindrome(): array
    {
        return [
            ['level', true],
            ['civic', true],
            ['a', true],
            ['', false],     // po našoj implementaciji prazan string NIJE palindrom
            ['ab', false],
            ['hello', false],
        ];
    }

    #[DataProvider('providePalindrome')]
    public function testIsPalindrome(string $w, bool $isPal): void
    {
        self::assertSame($isPal, $this->svc->isPalindrome($w));
    }

    public static function provideAlmost(): array
    {
        return [
            ['abca', true],     // remove 'b' => 'aca'
            ['racecars', true], // remove 's' => 'racecar'
            ['abab', true],     // remove 'a' (left) => 'bab'
            ['level', false],   // već palindrom => almost=false
            ['a', false],
            ['', false],
            ['abc', false],
        ];
    }

    #[DataProvider('provideAlmost')]
    public function testIsAlmostPalindrome(string $w, bool $expected): void
    {
        self::assertSame($expected, $this->svc->isAlmostPalindrome($w));
    }

    public function testAnalyze(): void
    {
        $r = $this->svc->analyze('level');
        self::assertSame(3, $r['uniqueLetters']); // l,e,v
        self::assertTrue($r['isPalindrome']);
        self::assertFalse($r['isAlmostPalindrome']);
        self::assertSame(6, $r['score']); // 3 + 3
    }
}
