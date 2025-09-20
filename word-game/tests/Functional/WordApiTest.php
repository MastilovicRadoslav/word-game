<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

# Testiram pravi HTTP sloj (routing, controller, DI, servisi, JSON).
# Izoluje se od produkcijskog rječnika pisanjem testnog fajla.
# Pokriva “sretan put”, greške validacije, i semantičku grešku (422).
final class WordApiTest extends WebTestCase
{
    // Prije sviih testova, ovaj metod kreira/prekuca testni rječnik u var/dictionaries/words.txt.
    // Time funkcionalni testovi postaju deterministični i ne zavise od “production” words.txt.
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
            'racecar', 'racecars', 'abca', 'noon','madam','refer','rotor','stats','wow' // dodano radi testova
        ];
        file_put_contents($dictionary, implode("\n", $words) . "\n");
    }

    // Svaki element je [ulazna riječ, očekivano palindrom?, očekivano almost?, očekivani HTTP status].
    // Palindromi i almost-palindromi su validni (200), van rječnika je 422, loš format je 400.
    public static function provideWords(): array
    {
        return [
            // palindrome
            ['level',    true,  false, 200],
            ['Racecar',  true,  false, 200],
            ['noon',   true,  false, 200],
            ['madam',  true,  false, 200],
            ['refer',  true,  false, 200],
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

    // glavni test
    #[DataProvider('provideWords')]
    public function testScore(string $word, bool $pal, bool $almost, int $expectedStatus = 200): void
    {

        // Stvara testni HTTP klijent i šalje stvaran POST na endpoint.
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/words/score',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['word' => $word], JSON_THROW_ON_ERROR)
        );

        // Verifikuje status (200/422/400).
        $this->assertResponseStatusCodeSame($expectedStatus);

        // Ako je status 200, čita JSON i provjerava:
        // Normalizacija je lowercase.
        // Flagovi isPalindrome i isAlmostPalindrome odgovaraju očekivanju.
        // Polje score postoji
        if ($expectedStatus === 200) {
            $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $this->assertSame(strtolower($word), $data['normalized']);
            $this->assertSame($pal, $data['isPalindrome']);
            $this->assertSame($almost, $data['isAlmostPalindrome']);
            $this->assertArrayHasKey('score', $data);

            // Specifične provjere za dvije riječi
            if (strtolower($word) === 'racecar') {
                self::assertSame(1, $data['uniqueLetters']); // samo 'e'
                self::assertSame(4, $data['score']);
            }

            if (strtolower($word) === 'abca') {
                self::assertSame(2, $data['uniqueLetters']); // b, c
                self::assertSame(4, $data['score']);
            }
        }
    }
}
