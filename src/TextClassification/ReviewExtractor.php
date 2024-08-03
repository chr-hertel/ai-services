<?php

declare(strict_types=1);

namespace Stoffel\AzureAi\TextClassification;

use Symfony\Component\DomCrawler\Crawler;

final readonly class ReviewExtractor
{
    public function __construct()
    {
    }

    public function extract(string $html): string
    {
        $crawler = new Crawler($html);

        return $crawler->filter('head script[type="application/ld+json"]')->text();
    }
}
