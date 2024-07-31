<?php

declare(strict_types=1);

namespace Stoffel\AzureAi\Command;

use Stoffel\AzureAi\AiVision;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

#[AsCommand('example:ai-vision', 'Command to test Azure AI Vision')]
final class AiVisionCommand extends Command
{
    public function __construct(
        private readonly AiVision $aiVision,
        private readonly string $dataPath,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Using AI Vision');

        $images = $this->getImages();

        do {
            $image = $io->choice('Select an image', array_values(array_map(fn (SplFileInfo $image) => $image->getPathname(), $images)));
            $feature = $io->choice('Select an analysis', AiVision::FEATURES);

            $io->text(sprintf('Analyzing image %s ...', $image));

            $response = $this->aiVision->analyze($image, $feature);

            $io->newLine();
            $io->comment(sprintf('%s Result:', $feature));
            $io->block($response);
        } while ($io->confirm('Do you want to analyze another image?'));

        $io->text('Okay, bye! 👋');

        return Command::SUCCESS;
    }

    /**
     * @return SplFileInfo[]
     */
    private function getImages(): array
    {
        $images = (new Finder())
            ->in($this->dataPath)
            ->files()
            ->sortByName()
            ->name('*.jpg');

        return iterator_to_array($images);
    }
}
