<?php

declare(strict_types=1);

namespace Stoffel\AzureAi\TextClassification;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function Symfony\Component\String\u;

final readonly class ReviewTransformer
{
    private const RATING_CLASS_MAP = [
        1 => 'flop',
        2 => 'flop',
        3 => 'bad',
        4 => 'bad',
        5 => 'okay',
        6 => 'okay',
        7 => 'good',
        8 => 'good',
        9 => 'hit',
        10 => 'hit',
    ];

    public function __construct(
        private Filesystem $filesystem,
        private string $dataPath,
        private string $projectName,
        private string $containerName,
    ) {
    }

    public function transform(): void
    {
        $reviews = $this->getRawReviews();
        $documents = [];

        foreach ($reviews as $review) {
            $data = json_decode($review->getContents(), true);
            $id = u($review->getFilename())->before('.json')->after('review')->toString();
            $file = sprintf('review%s.txt', $id);

            if (!isset($data['reviewRating']['ratingValue']) || !isset($data['reviewBody'])) {
                continue;
            }

            $documents[] = [
                'location' => $file,
                'language' => 'en-us',
                'dataset' => 'Train',
                'class' => [
                    'category' => self::RATING_CLASS_MAP[$data['reviewRating']['ratingValue']],
                ],
            ];

            $this->filesystem->dumpFile(
                sprintf('%s/reviews/txt/%s', $this->dataPath, $file),
                $data['reviewBody'],
            );
        }

        $this->writeLabelClassification($documents);
    }

    /**
     * @return SplFileInfo[]
     */
    private function getRawReviews(): array
    {
        $files = (new Finder())
            ->in(sprintf('%s/reviews/raw', $this->dataPath))
            ->sortByName()
            ->name('*.json');

        return iterator_to_array($files);
    }

    private function writeLabelClassification(array $documents): void
    {
        $labelClassification = [
            'projectFileVersion' => '2022-05-01',
            'stringIndexType' => 'Utf16CodeUnit',
            'metadata' => [
                'projectKind' => 'CustomSingleLabelClassification',
                'storageInputContainerName' => $this->containerName,
                'settings' => [],
                'projectName' => $this->projectName,
                'multilingual' => false,
                'description' => '',
                'language' => 'en-us',
            ],
            'assets' => [
                'projectKind' => 'CustomSingleLabelClassification',
                'classes' => array_map(fn ($value) => ['category' => $value], array_values(array_unique(self::RATING_CLASS_MAP))),
                'documents' => $documents,
            ],
        ];

        $this->filesystem->dumpFile(
            sprintf('%s/reviews/label_classification.json', $this->dataPath),
            json_encode($labelClassification, JSON_PRETTY_PRINT),
        );
    }
}
