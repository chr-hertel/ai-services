<?php

declare(strict_types=1);

namespace Stoffel\AzureAi\CognitiveServices;

final class SentimentAnalysisResultParser
{
    public function extractResult(array $result): string
    {
        $document = $result['results']['documents'][0];

        $text = 'Overall sentiment: '.$document['sentiment'].PHP_EOL;
        $text .= ' - Positive confidence: '.round($document['confidenceScores']['positive'] * 100, 2).'%'.PHP_EOL;
        $text .= ' - Neutral confidence: '.round($document['confidenceScores']['neutral'] * 100, 2).'%'.PHP_EOL;
        $text .= ' - Negative confidence: '.round($document['confidenceScores']['negative'] * 100, 2).'%'.PHP_EOL;

        $text .= PHP_EOL.'Sentiment per sentence:'.PHP_EOL;
        foreach ($document['sentences'] as $sentence) {
            $text .= ' - '.$sentence['sentiment'].': '.$sentence['text'];
            $text .= sprintf(
                ' (Positive: %.2f%%, Neutral: %.2f%%, Negative: %.2f%%)'.PHP_EOL,
                $sentence['confidenceScores']['positive'] * 100,
                $sentence['confidenceScores']['neutral'] * 100,
                $sentence['confidenceScores']['negative'] * 100
            );
        }

        return $text;
    }
}
