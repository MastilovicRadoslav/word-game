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

#[AsCommand(
    name: 'app:score-word',
    description: 'Score a word using dictionary and palindrome rules.'
)]
final class ScoreWordCommand extends Command
{
    public function __construct(
        private readonly DictionaryService $dict,
        private readonly WordGameService $game
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('word', InputArgument::REQUIRED, 'Word to score')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Output JSON');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io   = new SymfonyStyle($input, $output);
        $raw  = (string) $input->getArgument('word');
        $word = strtolower(trim($raw));

        if ($word === '' || !preg_match('/^[a-z]+$/', $word)) {
            $err = ['error' => 'Only letters a-z allowed.'];
            if ($input->getOption('json')) {
                $io->writeln(json_encode($err, JSON_UNESCAPED_SLASHES));
            } else {
                $io->error($err['error']);
            }
            return Command::INVALID;
        }

        if (!$this->dict->isValidWord($word)) {
            $err = ['error' => 'Word is not in the English dictionary.'];
            if ($input->getOption('json')) {
                $io->writeln(json_encode($err, JSON_UNESCAPED_SLASHES));
            } else {
                $io->warning($err['error']);
            }
            return Command::INVALID;
        }

        $analysis = $this->game->analyze($word);
        $payload = [
            'word'               => $raw,
            'normalized'         => $word,
            'uniqueLetters'      => $analysis['uniqueLetters'],
            'isPalindrome'       => $analysis['isPalindrome'],
            'isAlmostPalindrome' => $analysis['isAlmostPalindrome'],
            'score'              => $analysis['score'],
        ];

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
