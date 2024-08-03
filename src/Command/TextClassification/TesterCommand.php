<?php

declare(strict_types=1);

namespace Stoffel\AzureAi\Command\TextClassification;

use Stoffel\AzureAi\TextClassification\Tester;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('example:text-classification:test', 'Command to test Azure AI Text Classification')]
final class TesterCommand extends Command
{
    public function __construct(
        private readonly Tester $tester,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Using Custom Text Classification');

        do {
            $text = $io->ask('Enter a text to classify');

            $io->text('Classifying text ...');
            $result = $this->tester->classify($text);

            $io->newLine();
            $io->comment('Result:');
            $io->block($result);
        } while ($io->confirm('Do you want to classify another text?'));

        $io->text('Okay, bye! 👋');

        return Command::SUCCESS;
    }
}
