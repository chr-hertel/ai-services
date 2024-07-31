<?php

declare(strict_types=1);

namespace Stoffel\AzureAi\AiVision;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Client
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $endpoint,
        private string $key,
    ) {
    }

    public function request(string $path, string $imageData, array $query): array
    {
        try {
            $response = $this->httpClient->request('POST', $this->endpoint.$path, [
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => $this->key,
                    'Content-Type' => 'application/octet-stream',
                ],
                'query' => $query,
                'body' => $imageData,
            ]);

            return $response->toArray();
        } catch (ClientException $e) {
            $error = json_decode($e->getResponse()->getContent(false), true);

            throw new \Exception($error['error']['message']);
        }
    }
}
