<?php

declare(strict_types=1);

namespace Stoffel\AzureAi\TextClassification;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class ReviewDownloader
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private Filesystem $filesystem,
        private ReviewExtractor $reviewExtractor,
        private LoggerInterface $logger,
        private string $dataPath,
    ) {
    }

    public function download(): void
    {
        $responses = [];
        for ($i = 0; $i < 100; ++$i) {
            $reviewId = $this->getRandomReviewId();
            $file = $this->getReviewFilename($reviewId);

            if ($this->filesystem->exists($file) || array_key_exists($reviewId, $responses)) {
                continue;
            }

            $url = sprintf('https://www.imdb.com/review/rw%d/', $reviewId);
            $responses[$reviewId] = $this->httpClient->request('GET', $url);
        }

        foreach ($responses as $reviewId => $response) {
            try {
                $this->filesystem->dumpFile(
                    $this->getReviewFilename($reviewId),
                    $this->reviewExtractor->extract($response->getContent()),
                );
            } catch (ClientException|\InvalidArgumentException $e) {
                $this->logger->info(sprintf('Failed to download review: %s', $e->getMessage()), ['exception' => $e]);
            } catch (ServerException) {
                throw new \RuntimeException('I guess IMDB blocked you now ... try again in 5-10 minutes');
            }
        }
    }

    private function getRandomReviewId(): int
    {
        return random_int(1000000, 2000000);
    }

    private function getReviewFilename(int $reviewId): string
    {
        return $this->dataPath.'/reviews/raw/review'.$reviewId.'.json';
    }
}
