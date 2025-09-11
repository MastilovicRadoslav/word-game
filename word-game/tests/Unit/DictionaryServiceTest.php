<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Service\DictionaryService;
use PHPUnit\Framework\TestCase;

final class DictionaryServiceTest extends TestCase
{
    private function makeTempDict(array $words): string
    {
        $path = tempnam(sys_get_temp_dir(), 'dict_');
        // tempnam kreira fajl — prepišemo ga sadržajem
        file_put_contents($path, implode("\n", $words) . "\n");
        return $path;
    }

    public function testLoadsWordsAndIsCaseInsensitive(): void
    {
        $path = $this->makeTempDict(['Level', 'hello', 'WORLD']);
        $dict = new DictionaryService($path);

        self::assertTrue($dict->isValidWord('level'));
        self::assertTrue($dict->isValidWord('world'));
        self::assertTrue($dict->isValidWord('HELLO'));
        self::assertFalse($dict->isValidWord('unknown'));
        @unlink($path);
    }

    public function testIgnoresEmptyLinesAndWhitespace(): void
    {
        $path = $this->makeTempDict(['  civic  ', '', '  ', 'kayak']);
        $dict = new DictionaryService($path);

        self::assertTrue($dict->isValidWord('civic'));
        self::assertTrue($dict->isValidWord('kayak'));
        self::assertFalse($dict->isValidWord(''));
        @unlink($path);
    }

    public function testEmptyFileLeadsToNoMatches(): void
    {
        $path = $this->makeTempDict([]);
        $dict = new DictionaryService($path);

        self::assertFalse($dict->isValidWord('anything'));
        @unlink($path);
    }
}
