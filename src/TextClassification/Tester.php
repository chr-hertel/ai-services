<?php

declare(strict_types=1);

namespace Stoffel\AzureAi\TextClassification;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Tester
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $endpoint,
        private string $key,
        private string $projectName,
        private string $deploymentName,
    ) {
    }

    public function classify(string $text): string
    {
        $headers = [
            'Ocp-Apim-Subscription-Key' => $this->key,
            'Content-Type' => 'application/json',
        ];

        try {
            $response = $this->httpClient->request('POST', $this->endpoint.'language/analyze-text/jobs', [
                'headers' => $headers,
                'query' => ['api-version' => '2022-10-01-preview'],
                'json' => [
                    'displayName' => 'test',
                    'analysisInput' => [
                        'documents' => [
                            ['id' => '1', 'text' => $text, 'language' => 'en-us'],
                        ],
                    ],
                    'tasks' => [
                        [
                            'kind' => 'CustomSingleLabelClassification',
                            'parameters' => [
                                'projectName' => $this->projectName,
                                'deploymentName' => $this->deploymentName,
                            ],
                        ],
                    ],
                ],
            ]);

            $responseHeaders = $response->getHeaders();
        } catch (ClientException $e) {
            $error = json_decode($e->getResponse()->getContent(false), true);

            throw new \Exception($error['error']['message']);
        }

        if (202 !== $response->getStatusCode() || !isset($responseHeaders['operation-location'][0])) {
            throw new \Exception('Unexpected response from the API');
        }

        sleep(5);

        $resultResponse = $this->httpClient->request('GET', $responseHeaders['operation-location'][0], [
            'headers' => $headers,
        ]);

        try {
            $result = $resultResponse->toArray();
        } catch (ClientException $e) {
            $error = json_decode($e->getResponse()->getContent(false), true);

            throw new \Exception($error['error']['message']);
        }

        if (!isset($result['status']) || 'succeeded' !== $result['status']) {
            throw new \Exception('Unexpected response from the API');
        }

        return sprintf('%s (Confidence: %f)',
            $result['tasks']['items'][0]['results']['documents'][0]['class'][0]['category'],
            $result['tasks']['items'][0]['results']['documents'][0]['class'][0]['confidenceScore'],
        );
    }
}
