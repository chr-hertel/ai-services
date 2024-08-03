<?php

declare(strict_types=1);

namespace Stoffel\AzureAi\Command\TextClassification;

use Stoffel\AzureAi\TextClassification\ReviewTransformer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('example:text-classification:transform', 'Command to transform text classification data')]
final class TransformCommand extends Command
{
    public function __construct(
        private readonly ReviewTransformer $reviewTransformer,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Transforming Text Classification Data');

        $io->text('Starting to transform reviews ...');

        $this->reviewTransformer->transform();

        $io->success('Transformed all reviews!');

        return Command::SUCCESS;
    }
}
