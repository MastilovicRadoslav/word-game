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
            ['noon', true],
            ['madam', true],
            ['refer', true],
            ['rotor', true],
            ['stats', true],
            ['wow', true],
            ['a', true],
            ['', false],
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
        // level: l(2), e(2), v(1) => singletons=1; pal=true => 1+3=4
        $r = $this->svc->analyze('level');
        self::assertSame(1, $r['uniqueLetters']);
        self::assertTrue($r['isPalindrome']);
        self::assertFalse($r['isAlmostPalindrome']);
        self::assertSame(4, $r['score']);
    }

    public function testAnalyzeExamples(): void
    {
        // kayak: k(2), a(2), y(1) => 1 + 3 = 4
        $r = $this->svc->analyze('kayak');
        self::assertSame(1, $r['uniqueLetters']);
        self::assertTrue($r['isPalindrome']);
        self::assertSame(4, $r['score']);

        // deed: d(2), e(2) => 0 + 3 = 3
        $r = $this->svc->analyze('deed');
        self::assertSame(0, $r['uniqueLetters']);
        self::assertTrue($r['isPalindrome']);
        self::assertSame(3, $r['score']);

        // abca: a(2), b(1), c(1) => 2 + 2 = 4
        $r = $this->svc->analyze('abca');
        self::assertSame(2, $r['uniqueLetters']);
        self::assertFalse($r['isPalindrome']);
        self::assertTrue($r['isAlmostPalindrome']);
        self::assertSame(4, $r['score']);

        // racecar: r(2), a(2), c(2), e(1) => 1 + 3 = 4
        $r = $this->svc->analyze('racecar');
        self::assertSame(1, $r['uniqueLetters']);
        self::assertTrue($r['isPalindrome']);
        self::assertSame(4, $r['score']);

        // hello: h(1), e(1), l(2), o(1) => 3 + 0 = 3
        $r = $this->svc->analyze('hello');
        self::assertSame(3, $r['uniqueLetters']);
        self::assertFalse($r['isPalindrome']);
        self::assertFalse($r['isAlmostPalindrome']);
        self::assertSame(3, $r['score']);
    }
}
