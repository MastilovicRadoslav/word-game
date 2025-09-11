<?php
declare(strict_types=1);

namespace App\Service;

final class DictionaryService
{
    /** @var array<string,true> */
    private array $words = [];

    public function __construct(private readonly string $dictionaryPath)
    {
        if (is_file($this->dictionaryPath)) {
            $lines = file($this->dictionaryPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            foreach ($lines as $line) {
                $w = strtolower(trim($line));
                if ($w !== '') {
                    $this->words[$w] = true;
                }
            }
        }
    }

    public function isValidWord(string $word): bool
    {
        return isset($this->words[strtolower($word)]);
    }

    // alias koji koristiš u kontroleru/testovima
    public function exists(string $word): bool
    {
        return $this->isValidWord($word);
    }
}
