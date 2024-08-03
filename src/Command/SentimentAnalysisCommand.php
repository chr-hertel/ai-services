<?php

declare(strict_types=1);

namespace Stoffel\AzureAi\Command;

use Stoffel\AzureAi\CognitiveServices\SentimentAnalysis;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

#[AsCommand('example:sentiment-analysis', 'Command to analyze the sentiment of a text')]
final class SentimentAnalysisCommand extends Command
{
    public function __construct(
        private readonly SentimentAnalysis $sentimentAnalysis,
        private readonly string $dataPath,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Sentiment Analysis');

        $comments = $this->getComments();

        do {
            $comment = $io->choice('Select a comment', array_values(array_map(fn (SplFileInfo $comment) => $comment->getPathname(), $comments)));

            $io->text(sprintf('Analyzing comment %s ...', $comment));

            $response = $this->sentimentAnalysis->analyzeSentiment($comment);

            $io->newLine();
            $io->comment('Result:');
            $io->block($response);
        } while ($io->confirm('Do you want to analyze another comment?'));

        $io->text('Okay, bye! 👋');

        return Command::SUCCESS;
    }

    /**
     * @return SplFileInfo[]
     */
    private function getComments(): array
    {
        $images = (new Finder())
            ->in(sprintf('%s/comments', $this->dataPath))
            ->files()
            ->sortByName()
            ->name('*.txt');

        return iterator_to_array($images);
    }
}
