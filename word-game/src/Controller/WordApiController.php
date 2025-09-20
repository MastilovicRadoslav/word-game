<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\DictionaryService;
use App\Service\WordGameService;
use Symfony\Component\HttpFoundation\JsonResponse; # Symfony klasa za kreiranje HTTP odgovora u JSON formatu.
use Symfony\Component\HttpFoundation\Request; # Predstavlja dolazni HTTP zahtjev (request)
use Symfony\Component\Routing\Annotation\Route; # Ovo je atribut/annotacija kojom definišeš rutu za metod u kontroleru:

final class WordApiController
{
    #[Route('/api/words/score', name: 'api_words_score', methods: ['POST'])]
    public function score(Request $request, DictionaryService $dict, WordGameService $game): JsonResponse
    {
        // 1) Parsiranje JSON-a
        try {
            $payload = $request->toArray(); # toArray() automatski parsira JSON i baci izuzetak ako nije validan JSON → ti vraćaš 400 Invalid JSON body.
        } catch (\Throwable) {
            return new JsonResponse(['error' => 'Invalid JSON body.'], 400);
        }

        // 2) Polje "word" obavezno
        $raw = $payload['word'] ?? null;
        if (!$raw || !\is_string($raw)) { # Provjera da word postoji i da je string.
            return new JsonResponse(['error' => 'Missing "word".'], 400);
        }

        // 3) Normalizacija + regex validacija, dozvoljavam samo ASCII a–z (bez brojeva, razmaka, znakova), takodje sam mogao pozvati $game->normalize($raw)
        $word = strtolower(trim($raw));
        if (!preg_match('/^[a-z]+$/', $word)) {
            return new JsonResponse(['error' => 'Only letters a-z allowed.'], 400);
        }

        // 4) Rječnik => 422, dozvoli samo riječi iz rječnika
        if (!$dict->exists($word)) {
            return new JsonResponse(['error' => 'Word is not in the English dictionary.'], 422);
        }

        // 5) Analiza
        $analysis = $game->analyze($word);

        // Vraćam sve tražene podatke; word ostaje original (radi UI highlight-a), normalized za prikaz/debug; ostalo iz servisa
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
