<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Service\DictionaryService;
use PHPUnit\Framework\TestCase; # klasa dobija sve mogućnosti PHPUnit-a.

# Ovo je unit test klasa za DictionaryService. Da li servis pravilno radi ono sto smo napisali u njemu
final class DictionaryServiceTest extends TestCase
{
    # Helper metoda koja napravi privremeni fajl sa zadatim riječima:
    private function makeTempDict(array $words): string
    {
        $path = tempnam(sys_get_temp_dir(), 'dict_'); # napravi prazni fajl u sistemskom tmp folderu.
        file_put_contents($path, implode("\n", $words) . "\n"); # upiše sve riječi u taj fajl, po jednu u redu.
        return $path; # vraća putanju do tog fajla.
    }

    # Testiram da je servis case-insensitive i da vraća false za riječi koje nisu u rječniku.
    public function testLoadsWordsAndIsCaseInsensitive(): void
    {
        $path = $this->makeTempDict(['Level', 'hello', 'WORLD']); # Pravi privremeni fajl sa riječima: ['Level', 'hello', 'WORLD']
        $dict = new DictionaryService($path); # Kreira novi DictionaryService sa tim fajlom.

        self::assertTrue($dict->isValidWord('level')); // jer 'Level' -> lowercase
        self::assertTrue($dict->isValidWord('world')); // jer 'Level' -> lowercase
        self::assertTrue($dict->isValidWord('HELLO')); // jer 'Level' -> lowercase
        self::assertFalse($dict->isValidWord('unknown')); // nije u fajlu
        @unlink($path);
    }

    # Testiram da servis ignoriše prazne linije i whitespace.
    public function testIgnoresEmptyLinesAndWhitespace(): void
    {
        $path = $this->makeTempDict(['  civic  ', '', '  ', 'kayak']); // Privremeni fajl: [' civic ', '', ' ', 'kayak'].
        $dict = new DictionaryService($path); // DictionaryService ih normalizuje (trim + lowercase).

        self::assertTrue($dict->isValidWord('civic')); // validan
        self::assertTrue($dict->isValidWord('kayak')); // validan
        self::assertFalse($dict->isValidWord('')); // nevalidan je prazan string
        @unlink($path);
    }

    # Testiram ponašanje kad rječnik nema nijednu riječ.
    public function testEmptyFileLeadsToNoMatches(): void
    {
        $path = $this->makeTempDict([]); // Privremeni fajl prazan.
        $dict = new DictionaryService($path);

        self::assertFalse($dict->isValidWord('anything')); // Provjera: bilo koja riječ (anything) nije validna.
        @unlink($path);
    }
}
