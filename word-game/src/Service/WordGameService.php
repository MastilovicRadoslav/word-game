<?php
declare(strict_types=1);

namespace App\Service;

final class WordGameService
{
    # normalizovanje rijeci, ne koristim jer tamo vec radi u kontroleru
    public function normalize(string $input): string
    {
        $lower = strtolower($input); # sve u lowercase
        $norm = preg_replace('/[^a-z]/', '', $lower); # uklanja sve što nije a–z (ASCII)
        return $norm ?? ''; # vraća čist engleski niz bez razmaka, brojeva i znakova
    }

    # riječ je već normalizovana
    public function analyze(string $normalized): array
    {
        $isPal    = $this->isPalindrome($normalized);
        $isAlmost = !$isPal && $this->isAlmostPalindrome($normalized);

        // broj slova koja se pojavljuju TAČNO jednom
        $freq = count_chars($normalized, 1); // [ascii => count]
        $singletons = 0;
        foreach ($freq as $count) {
            if ($count === 1) {
                $singletons++;
            }
        }

        # saberi za bodove
        $score = $singletons + ($isPal ? 3 : 0) + ($isAlmost ? 2 : 0);

        return [
            'uniqueLetters'       => $singletons,       // sada znači "non-repeating"
            'isPalindrome'        => $isPal,
            'isAlmostPalindrome'  => $isAlmost,
            'score'               => $score,
        ];
    }

    # provjera da li je riječ palindrom
    public function isPalindrome(string $w): bool
    {
        $n = strlen($w); # uzimam dužinu
        if ($n === 0) return false; # ako je dužina 0 vraćam false
        $i = 0; $j = $n - 1; # dva pokazivača (i i j) sa krajeva prema sredini.
        while ($i < $j) {
            if ($w[$i] !== $w[$j]) return false; # ako su slova različita to znači da to nije palindrom
            $i++; $j--; # ako su ista nastaviti provjeravati dok dodjes do sredine kraja niza
        }
        return true;
    }

    # provjera da li je riječ skoro palindrom
    public function isAlmostPalindrome(string $w): bool
    {
        $n = strlen($w); # dužina
        if ($n <= 1) return false; # ako nema dužine onda je false

        $i = 0; $j = $n - 1; # i je početak niza a j je kraj niza
        while ($i < $j) { # petlja
            if ($w[$i] === $w[$j]) { $i++; $j--; continue; }
            // skini jedan znak lijevo ili desno pa provjeri
            return $this->isPalindromeRange($w, $i + 1, $j) || $this->isPalindromeRange($w, $i, $j - 1);
        }
        return false; // već je palindrom => almost=false
    }

    private function isPalindromeRange(string $w, int $i, int $j): bool
    {
        while ($i < $j) {
            if ($w[$i] !== $w[$j]) return false;
            $i++; $j--;
        }
        return true;
    }
}
