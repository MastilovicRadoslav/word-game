<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class WordApiTest extends WebTestCase
{
    public static function setUpBeforeClass(): void
    {
        $projectDir = \dirname(__DIR__, 1);
        $dictionary = $projectDir . '/var/dictionaries/words.txt';
        if (!is_dir(\dirname($dictionary))) {
            mkdir(\dirname($dictionary), 0777, true);
        }
        if (!file_exists($dictionary)) {
            file_put_contents($dictionary, "level\nhello\nworld\nkayak\ndeed\ncivic\nlever\npearl\nriver\nradar\n");
        }
    }

    public function testScorePalindrome(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/words/score',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['word' => 'Level'], JSON_THROW_ON_ERROR)
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('Level', $data['word']);
        $this->assertSame('level', $data['normalized']);
        $this->assertTrue($data['isPalindrome']);
        $this->assertFalse($data['isAlmostPalindrome']);
        $this->assertSame(6, $data['score']); // 3 unikatna slova + 3 boda za palindrom
    }

    public function testRejectWordNotInDictionary(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/words/score',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['word' => 'zzzzzz'], JSON_THROW_ON_ERROR)
        );
        $this->assertResponseStatusCodeSame(422);
    }
}
