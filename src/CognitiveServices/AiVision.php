<?php

declare(strict_types=1);

namespace Stoffel\AzureAi\CognitiveServices;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class AiVision
{
    public const FEATURES = [
        'Caption',
        'DenseCaptions',
        'Objects',
        'People',
        'Read',
        // 'SmartCrops',
        'Tags',
    ];

    public function __construct(
        private Filesystem $filesystem,
        private HttpClientInterface $httpClient,
        private AiVisionResultParser $resultParser,
        private string $endpoint,
        private string $key,
    ) {
    }

    public function analyzeImage(string $imagePath, string $feature): string
    {
        $image = $this->filesystem->readFile($imagePath);

        try {
            $response = $this->httpClient->request('POST', $this->endpoint.'computervision/imageanalysis:analyze', [
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => $this->key,
                    'Content-Type' => 'application/octet-stream',
                ],
                'query' => [
                    'features' => $feature,
                    'model-version' => 'latest',
                    'language' => 'en',
                    'api-version' => '2024-02-01',
                ],
                'body' => $image,
            ]);

            return $this->resultParser->extractResult($response->toArray());
        } catch (ClientException $e) {
            $error = json_decode($e->getResponse()->getContent(false), true);

            throw new \Exception($error['error']['message']);
        }
    }
}
