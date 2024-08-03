<?php

declare(strict_types=1);

namespace Stoffel\AzureAi\Command\TextClassification;

use Stoffel\AzureAi\TextClassification\ReviewDownloader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('example:text-classification:download', 'Command to download text classification data')]
final class DownloadCommand extends Command
{
    public function __construct(
        private readonly ReviewDownloader $reviewDownloader,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Downloading Text Classification Data');

        $batches = 50;
        $io->text(sprintf('Starting to download %d batches of 100 reviews each ...', $batches));

        $io->progressStart($batches);
        for ($i = 0; $i < $batches; ++$i) {
            $this->reviewDownloader->download();
            $io->progressAdvance();
        }
        $io->progressFinish();

        $io->success('Downloaded all reviews!');

        return Command::SUCCESS;
    }
}
