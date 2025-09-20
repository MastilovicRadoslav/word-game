<?php
declare(strict_types=1); # strogo provjerava tipove

namespace App\Service;



public interface IDictionaryService(){
    Task<bool> exists(string word);
}

final class DictionaryService : IDictionaryService()
{
    /** @var array<string,true> */
    private array $words = [];
    # konstruktor; otvori fajl sa riječima (words.txt); učita svaku liniju; normalizuje u lowercase i ukloni whitespace; Stavi riječ u $this->words kao ključ → O(1) lookup.
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

    # provjerava postoji li riječ u $this->words (case-insensitive).
    function isValidWord(string $word): bool
    {
        return isset($this->words[strtolower($word)]);
    }

    // alias koji koristiš u kontroleru/testovima
    public override exists(string $word): bool
    {
        return isValidWord(word);
    }
}
