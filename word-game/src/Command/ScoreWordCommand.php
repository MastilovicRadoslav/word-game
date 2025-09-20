<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\DictionaryService;
use App\Service\WordGameService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;

// Atribut AsCommand automatski registruje komandu pod imenom app:score-word.
#[AsCommand(
    name: 'app:score-word',
    description: 'Score a word using dictionary and palindrome rules.' // description je kratki opis koji se vidi u php bin/console list i php bin/console app:score-word -h.
)]

final class ScoreWordCommand extends Command
{
    // Symfony injektuje  DictionaryService (provjera rječnika) i WordGameService (logika bodovanja).
    public function __construct(
        private readonly DictionaryService $dict,
        private readonly WordGameService $game
    ) {
        parent::__construct();
    }

    // argument + opcija
    protected function configure(): void
    {
        $this
            ->addArgument('word', InputArgument::REQUIRED, 'Word to score') // Argument word je obavezan.
            ->addOption('json', null, InputOption::VALUE_NONE, 'Output JSON'); // Opcija --json mijenja izlaz u JSON (umjesto “ljudskog” formata).
    }

    // glavna logika
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io   = new SymfonyStyle($input, $output); // SymfonyStyle daje lijepe poruke (success, error, warning).
        $raw  = (string) $input->getArgument('word');
        $word = strtolower(trim($raw)); // Normalizujem osnovno (lower+trim).

        // Validacija formata - Dozvoljena su samo slova a–z. Inače ispisuje poruku i vraća exit code = 2.
        if ($word === '' || !preg_match('/^[a-z]+$/', $word)) {
            $err = ['error' => 'Only letters a-z allowed.'];
            if ($input->getOption('json')) {
                $io->writeln(json_encode($err, JSON_UNESCAPED_SLASHES));
            } else {
                $io->error($err['error']);
            }
            return Command::INVALID;
        }

        // Provjera rjecnika - Ako riječ nije u rječniku → poruka (warning ili JSON error), exit code 2.
        if (!$this->dict->isValidWord($word)) {
            $err = ['error' => 'Word is not in the English dictionary.'];
            if ($input->getOption('json')) {
                $io->writeln(json_encode($err, JSON_UNESCAPED_SLASHES));
            } else {
                $io->warning($err['error']);
            }
            return Command::INVALID;
        }

        // Analiza i ispis
        $analysis = $this->game->analyze($word);
        // payload sa svim poljima 
        $payload = [
            'word'               => $raw,
            'normalized'         => $word,
            'uniqueLetters'      => $analysis['uniqueLetters'],
            'isPalindrome'       => $analysis['isPalindrome'],
            'isAlmostPalindrome' => $analysis['isAlmostPalindrome'],
            'score'              => $analysis['score'],
        ];

        // Ispis, JSON kad je --json, “Ljudski” format preko SymfonyStyle::success() kad nije
        if ($input->getOption('json')) {
            $io->writeln(json_encode($payload, JSON_UNESCAPED_SLASHES));
        } else {
            $io->success(sprintf(
                'Word: %s | normalized: %s | unique: %d | pal: %s | almost: %s | score: %d',
                $payload['word'],
                $payload['normalized'],
                $payload['uniqueLetters'],
                $payload['isPalindrome'] ? 'yes' : 'no',
                $payload['isAlmostPalindrome'] ? 'yes' : 'no',
                $payload['score']
            ));
        }

        return Command::SUCCESS;
    }
}
