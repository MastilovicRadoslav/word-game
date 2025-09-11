<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\DictionaryService;
use App\Service\WordGameService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class WordApiController
{
    #[Route('/api/words/score', name: 'api_words_score', methods: ['POST'])]
    public function score(Request $request, DictionaryService $dict, WordGameService $game): JsonResponse
    {
        // 1) Parsiranje JSON-a
        try {
            $payload = $request->toArray();
        } catch (\Throwable) {
            return new JsonResponse(['error' => 'Invalid JSON body.'], 400);
        }

        // 2) Polje "word"
        $raw = $payload['word'] ?? null;
        if (!$raw || !\is_string($raw)) {
            return new JsonResponse(['error' => 'Missing "word".'], 400);
        }

        // 3) Normalizacija + regex validacija
        $word = strtolower(trim($raw));
        if (!preg_match('/^[a-z]+$/', $word)) {
            return new JsonResponse(['error' => 'Only letters a-z allowed.'], 400);
        }

        // 4) Rječnik => 422
        if (!$dict->exists($word)) {
            return new JsonResponse(['error' => 'Word is not in the English dictionary.'], 422);
        }

        // 5) Analiza
        $analysis = $game->analyze($word);

        return new JsonResponse([
            'word'                => $raw,
            'normalized'          => $word,
            'uniqueLetters'       => $analysis['uniqueLetters'],
            'isPalindrome'        => $analysis['isPalindrome'],
            'isAlmostPalindrome'  => $analysis['isAlmostPalindrome'],
            'score'               => $analysis['score'],
        ], 200);
    }
}
