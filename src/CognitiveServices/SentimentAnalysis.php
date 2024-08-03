<?php

declare(strict_types=1);

namespace Stoffel\AzureAi\CognitiveServices;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class SentimentAnalysis
{
    public function __construct(
        private Filesystem $filesystem,
        private HttpClientInterface $httpClient,
        private SentimentAnalysisResultParser $resultParser,
        private string $endpoint,
        private string $key,
    ) {
    }

    public function analyzeSentiment(string $file): string
    {
        $text = $this->filesystem->readFile($file);

        try {
            $response = $this->httpClient->request('POST', $this->endpoint.'language/:analyze-text', [
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => $this->key,
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'api-version' => '2023-04-01',
                ],
                'json' => [
                    'kind' => 'SentimentAnalysis',
                    'analysisInput' => [
                        'documents' => [
                            ['id' => 'first', 'text' => $text, 'language' => 'en'],
                        ],
                    ],
                    'parameters' => ['opinionMining' => true],
                ],
            ]);

            return $this->resultParser->extractResult($response->toArray());
        } catch (ClientException $e) {
            $error = json_decode($e->getResponse()->getContent(false), true);

            throw new \Exception($error['error']['message']);
        }
    }
}
