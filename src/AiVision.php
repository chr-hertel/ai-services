<?php

declare(strict_types=1);

namespace Stoffel\AzureAi;

use Stoffel\AzureAi\AiVision\Client;
use Stoffel\AzureAi\AiVision\Parser;

final readonly class AiVision
{
    public const FEATURES = [
        'Caption',
        'DenseCaptions',
        'Objects',
        'People',
        'Read',
        // 'SmartCrops',
        'Tags'
    ];

    public function __construct(
        private Client $client,
        private Parser $parser,
    ) {
    }

    public function analyze(string $imagePath, string $feature): string
    {
        $image = file_get_contents($imagePath);

        $response = $this->client->request('imageanalysis:analyze', $image, [
            'features' => $feature,
            'model-version' => 'latest',
            'language' => 'en',
            'api-version' => '2024-02-01',
        ]);

        return $this->parser->extractResult($response);
    }
}
