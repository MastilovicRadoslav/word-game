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
        $payload = json_decode((string) $request->getContent(), true);
        $input = (string) ($payload['word'] ?? '');
        $normalized = $game->normalize($input);

        if ($normalized === '') {
            return new JsonResponse(['error' => 'Invalid input: use letters A-Z only.'], 400);
        }
        if (!$dict->isValidWord($normalized)) {
            return new JsonResponse(['error' => 'Word is not in the English dictionary.'], 422);
        }

        $analysis = $game->analyze($normalized);

        return new JsonResponse([
            'word'                => $input,
            'normalized'          => $normalized,
            'uniqueLetters'       => $analysis['uniqueLetters'],
            'isPalindrome'        => $analysis['isPalindrome'],
            'isAlmostPalindrome'  => $analysis['isAlmostPalindrome'],
            'score'               => $analysis['score'],
        ], 200);
    }
}
