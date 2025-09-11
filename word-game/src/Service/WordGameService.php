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
        $isPal = $this->isPalindrome($normalized);
        $isAlmost = !$isPal && $this->isAlmostPalindrome($normalized);

        $uniqueLetters = strlen(count_chars($normalized, 3));
        $score = $uniqueLetters + ($isPal ? 3 : 0) + ($isAlmost ? 2 : 0);

        return [
            'uniqueLetters'       => $uniqueLetters,
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
            return $this->isPalindromeRange($w, $i + 1, $j) || $this->isPalindromeRange($w, $i, $j - 1);
        }
        return false; // ako je već palindrom, almost = false
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
