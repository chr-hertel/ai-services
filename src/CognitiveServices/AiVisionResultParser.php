<?php

declare(strict_types=1);

namespace Stoffel\AzureAi\CognitiveServices;

final readonly class AiVisionResultParser
{
    public function extractResult(array $response): string
    {
        if (array_key_exists('captionResult', $response)) {
            return $response['captionResult']['text'];
        }

        if (array_key_exists('readResult', $response)) {
            $text = '';
            foreach ($response['readResult']['blocks'] as $block) {
                foreach ($block['lines'] as $line) {
                    $text .= ' - '.$line['text'].PHP_EOL;
                }
            }

            return $text;
        }

        if (array_key_exists('denseCaptionsResult', $response)) {
            $text = '';
            foreach ($response['denseCaptionsResult']['values'] as $value) {
                $text .= ' - '.$value['text'].' (Confidence: '.round($value['confidence'], 2).')'.PHP_EOL;
            }

            return $text;
        }

        if (array_key_exists('peopleResult', $response)) {
            $people = $response['peopleResult']['values'];
            $confidencePeople = array_filter($people, fn ($person) => $person['confidence'] > 0.85);

            return sprintf('Found %d people, %d with confidence above 0.85', count($people), count($confidencePeople));
        }

        if (array_key_exists('objectsResult', $response)) {
            $text = 'Objects found:'.PHP_EOL;
            foreach ($response['objectsResult']['values'] as $value) {
                $text .= ' - '.$value['tags'][0]['name'].' (Confidence: '.round($value['tags'][0]['confidence'], 2).')'.PHP_EOL;
            }

            return $text;
        }

        if (array_key_exists('tagsResult', $response)) {
            $text = 'Tags found:'.PHP_EOL;
            foreach ($response['tagsResult']['values'] as $value) {
                $text .= ' - '.$value['name'].' (Confidence: '.round($value['confidence'], 2).')'.PHP_EOL;
            }

            return $text;
        }

        throw new \Exception('Unknown response format.');
    }
}
