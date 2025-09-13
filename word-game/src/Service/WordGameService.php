<?php
declare(strict_types=1);

namespace App\Service;

final class WordGameService
{
    public function normalize(string $input): string
    {
        $lower = strtolower($input);
        $norm = preg_replace('/[^a-z]/', '', $lower);
        return $norm ?? '';
    }

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

        $score = $singletons + ($isPal ? 3 : 0) + ($isAlmost ? 2 : 0);

        return [
            'uniqueLetters'       => $singletons,       // sada znači "non-repeating"
            'isPalindrome'        => $isPal,
            'isAlmostPalindrome'  => $isAlmost,
            'score'               => $score,
        ];
    }


    public function isPalindrome(string $w): bool
    {
        $n = strlen($w);
        if ($n === 0) return false;
        $i = 0; $j = $n - 1;
        while ($i < $j) {
            if ($w[$i] !== $w[$j]) return false;
            $i++; $j--;
        }
        return true;
    }

    public function isAlmostPalindrome(string $w): bool
    {
        $n = strlen($w);
        if ($n <= 1) return false;

        $i = 0; $j = $n - 1;
        while ($i < $j) {
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
