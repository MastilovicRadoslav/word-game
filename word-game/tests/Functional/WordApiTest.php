<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class WordApiTest extends WebTestCase
{
    public static function setUpBeforeClass(): void
    {
        // project dir = .../word-game
        $projectDir = \dirname(__DIR__, 2);
        $dictionary = $projectDir . '/var/dictionaries/words.txt';

        if (!is_dir(\dirname($dictionary))) {
            mkdir(\dirname($dictionary), 0777, true);
        }

        // Test rječnik (stalno ga prepiši da test bude determinističan)
        $words = [
            'level', 'hello', 'world', 'kayak', 'deed', 'civic', 'lever', 'pearl', 'river', 'radar',
            'racecar', 'racecars', 'abca' // dodano radi testova
        ];
        file_put_contents($dictionary, implode("\n", $words) . "\n");
    }


    public static function provideWords(): array
    {
        return [
            // palindrome
            ['level',    true,  false, 200],
            ['Racecar',  true,  false, 200],
            // almost-palindrome: ukloni 1 znak pa bude palindrome
            ['abca',     false, true,  200], // remove 'b' -> 'aca'
            ['racecars', false, true,  200], // remove 's' -> 'racecar'
            // nije u rječniku
            ['zzzzzz',   false, false, 422],
            // loš input
            ['',         false, false, 400],
            ['he!!o',    false, false, 400],
        ];
    }

    #[DataProvider('provideWords')]
    public function testScore(string $word, bool $pal, bool $almost, int $expectedStatus = 200): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/words/score',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['word' => $word], JSON_THROW_ON_ERROR)
        );

        $this->assertResponseStatusCodeSame($expectedStatus);

        if ($expectedStatus === 200) {
            $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $this->assertSame(strtolower($word), $data['normalized']);
            $this->assertSame($pal, $data['isPalindrome']);
            $this->assertSame($almost, $data['isAlmostPalindrome']);
            $this->assertArrayHasKey('score', $data);
        }
    }
}
