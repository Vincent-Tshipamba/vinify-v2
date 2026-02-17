<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CorpusPlagiarismAnalyzer
{
    public function analyze(string $targetText, Collection $sourceDocuments): array
    {
        $targetText = trim($targetText);
        if ($targetText === '') {
            return $this->emptyResult('');
        }

        $payloadSources = $sourceDocuments
            ->map(static function ($document): array {
                return [
                    'id' => $document->id,
                    'name' => $document->name ?? ('Document #' . $document->name),
                    'content' => trim((string) $document->content),
                ];
            })
            ->filter(static fn(array $item): bool => $item['content'] !== '')
            ->values()
            ->all();

        if ($payloadSources === []) {
            return $this->emptyResult($targetText);
        }

        $url = (string) config('services.flask.url', 'http://127.0.0.1:5050/check-plagiarism');

        $response = Http::acceptJson()
            ->asJson()
            ->connectTimeout((int) config('services.flask.connect_timeout', 10))
            ->timeout((int) config('services.flask.timeout', 180))
            ->post($url, [
                'document_text' => $targetText,
                'source_documents' => $payloadSources,
            ]);

        if (!$response->successful()) {
            Log::error('Flask plagiarism API request failed.', [
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException('Flask plagiarism API request failed with status ' . $response->status() . '.');
        }

        $json = $response->json();
        if (!is_array($json)) {
            throw new \RuntimeException('Invalid JSON response from Flask plagiarism API.');
        }

        $rawResult = $json['similarities'] ?? [];
        if (!is_array($rawResult)) {
            throw new \RuntimeException('Unexpected Flask plagiarism API response structure.');
        }

        $nested = $rawResult['similarities'] ?? [];
        $similarities = is_array($nested['similarities'] ?? null)
            ? array_values($nested['similarities'])
            : (is_array($rawResult['similarities'] ?? null) ? array_values($rawResult['similarities']) : []);

        $excerptedText = is_array($nested['excerpted_text'] ?? null)
            ? array_values($nested['excerpted_text'])
            : (is_array($rawResult['excerpted_text'] ?? null) ? array_values($rawResult['excerpted_text']) : []);

        return [
            'full_text' => $rawResult['full_text'] ?? $targetText,
            'highlighted_text' => $rawResult['highlighted_text'] ?? $targetText,
            'plagiarism_percentage' => (float) ($rawResult['plagiarism_percentage'] ?? 0),
            'similarities' => $similarities,
            'excerpted_text' => $excerptedText,
        ];
    }

    public function supportsHighlightedTextColumn(): bool
    {
        return Schema::hasColumn('text_analyses', 'highlighted_text');
    }

    public function supportsExcerptedTextColumn(): bool
    {
        return Schema::hasColumn('text_analyses', 'excerpted_text');
    }

    private function emptyResult(string $text): array
    {
        return [
            'full_text' => $text,
            'highlighted_text' => $text,
            'plagiarism_percentage' => 0.0,
            'similarities' => [],
            'excerpted_text' => [],
        ];
    }
}
