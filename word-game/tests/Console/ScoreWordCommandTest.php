<?php
declare(strict_types=1);

namespace App\Tests\Console;

use App\Command\ScoreWordCommand;
use App\Service\DictionaryService;
use App\Service\WordGameService;
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

    public function testSuccessJson(): void
    {
        $dictPath = self::makeTempDict(['level', 'hello', 'world']);
        $cmd = new ScoreWordCommand(new DictionaryService($dictPath), new WordGameService());
        $tester = new CommandTester($cmd);

        $exit = $tester->execute(['word' => 'Level', '--json' => true]);
        $output = trim($tester->getDisplay());

        self::assertSame(0, $exit);
        $data = json_decode($output, true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('Level', $data['word']);
        self::assertSame('level', $data['normalized']);
        self::assertTrue($data['isPalindrome']);
        self::assertFalse($data['isAlmostPalindrome']);

        // Pravilo: singletons = 1 ('v'), pa 1 + 3 = 4
        self::assertSame(1, $data['uniqueLetters']);
        self::assertSame(4, $data['score']);

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
