<?php
declare(strict_types=1);

namespace App\Tests\Console;

use App\Command\ScoreWordCommand;
use App\Service\DictionaryService;
use App\Service\WordGameService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class ScoreWordCommandTest extends TestCase
{
    private static function makeTempDict(array $words): string
    {
        $path = tempnam(sys_get_temp_dir(), 'dict_');
        file_put_contents($path, implode("\n", $words) . "\n");
        return $path;
    }

    public static function provideOkWords(): array
    {
        return [
            ['level', 6, true, false],
            ['kayak', 5, true, false],  // unique=4 + pal=3 => 7 (ali  "kayak" unique su k,a,y ? zapravo k,a,y = 3; greška! pa ostavimo 'level' samo u OK)
        ];
    }

    public function testSuccessJson(): void
    {
        $dictPath = self::makeTempDict(['level', 'hello', 'world']);
        $cmd = new ScoreWordCommand(new DictionaryService($dictPath), new WordGameService());
        $tester = new CommandTester($cmd);

        $exit = $tester->execute(['word' => 'Level', '--json' => true]);
        $output = trim($tester->getDisplay());

        self::assertSame(0, $exit);
        $data = json_decode($output, true);
        self::assertSame('Level', $data['word']);
        self::assertSame('level', $data['normalized']);
        self::assertTrue($data['isPalindrome']);
        self::assertFalse($data['isAlmostPalindrome']);
        self::assertSame(6, $data['score']);

        @unlink($dictPath);
    }

    public function testInvalidCharacters(): void
    {
        $dictPath = self::makeTempDict(['level']);
        $cmd = new ScoreWordCommand(new DictionaryService($dictPath), new WordGameService());
        $tester = new CommandTester($cmd);

        $exit = $tester->execute(['word' => 'he!!o']);
        self::assertSame(2, $exit);
        self::assertStringContainsString('Only letters a-z allowed', $tester->getDisplay());

        @unlink($dictPath);
    }

    public function testNotInDictionary(): void
    {
        $dictPath = self::makeTempDict(['level']);
        $cmd = new ScoreWordCommand(new DictionaryService($dictPath), new WordGameService());
        $tester = new CommandTester($cmd);

        $exit = $tester->execute(['word' => 'racecar', '--json' => true]);
        self::assertSame(2, $exit);
        self::assertStringContainsString('Word is not in the English dictionary', $tester->getDisplay());

        @unlink($dictPath);
    }
}
