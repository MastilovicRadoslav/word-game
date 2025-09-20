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
    //Napravi privremeni fajl u sys_get_temp_dir() i upiše date riječi (po jedna u redu).
    //Vraća putanju tog fajla.
    //Koristi se da testovi budu izolovani od “pravog” words.txt.
    private static function makeTempDict(array $words): string
    {
        $path = tempnam(sys_get_temp_dir(), 'dict_');
        file_put_contents($path, implode("\n", $words) . "\n");
        return $path;
    }

    // Testira da komanda radi za validnu riječ koja jeste u rječniku i da JSON mod (--json) ispisuje ispravna polja.
    public function testSuccessJson(): void
    {
        // Kreira temp rječnik sa ['level','hello','world'].
        $dictPath = self::makeTempDict(['level', 'hello', 'world']);
        $cmd = new ScoreWordCommand(new DictionaryService($dictPath), new WordGameService()); // Instancira komandu sa new DictionaryService($dictPath) i new WordGameService()
        $tester = new CommandTester($cmd); // CommandTester izvršava

        $exit = $tester->execute(['word' => 'Level', '--json' => true]);
        $output = trim($tester->getDisplay());

        self::assertSame(0, $exit); // exit je 0 (što znači Command::SUCCESS).
        $data = json_decode($output, true, 512, JSON_THROW_ON_ERROR); // JSON izlaz se parsira i provjeravaju se polja:
        self::assertSame('Level', $data['word']); // word == 'Level' (original), normalized == 'level'
        self::assertSame('level', $data['normalized']);
        self::assertTrue($data['isPalindrome']); // isPalindrome == true, isAlmostPalindrome == false
        self::assertFalse($data['isAlmostPalindrome']); // uniqueLetters == 1, score == 4 (po tvojoj trenutnoj “singleton” logici)

        // Pravilo: singletons = 1 ('v'), pa 1 + 3 = 4
        self::assertSame(1, $data['uniqueLetters']);
        self::assertSame(4, $data['score']);

        @unlink($dictPath); // briše temp fajl.
    }

    // Testira nevažeći ulaz (nedozvoljeni znakovi) treba da vrati INVALID i poruku o grešci.
    public function testInvalidCharacters(): void
    {
        $dictPath = self::makeTempDict(['level']); // rjecnik
        $cmd = new ScoreWordCommand(new DictionaryService($dictPath), new WordGameService());
        $tester = new CommandTester($cmd); // izvrsi

        $exit = $tester->execute(['word' => 'he!!o']);
        self::assertSame(2, $exit); // exit == 2 (što znači Command::INVALID).
        self::assertStringContainsString('Only letters a-z allowed', $tester->getDisplay()); // U izlazu postoji poruka 'Only letters a-z allowed'.

        @unlink($dictPath);
    }

    // testira da riječ nije u rječniku → komanda treba da vrati INVALID i poruku “Word is not in the English dictionary”.
    public function testNotInDictionary(): void
    {
        $dictPath = self::makeTempDict(['level']); //rečnik
        $cmd = new ScoreWordCommand(new DictionaryService($dictPath), new WordGameService());
        $tester = new CommandTester($cmd); // izvrši

        $exit = $tester->execute(['word' => 'racecar', '--json' => true]);
        self::assertSame(2, $exit); // exit == 2 (INVALID).
        self::assertStringContainsString('Word is not in the English dictionary', $tester->getDisplay()); // Izlaz sadrži poruku o tome da riječ nije u rječniku.

        @unlink($dictPath);
    }
}
