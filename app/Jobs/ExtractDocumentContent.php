<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\DocumentTextExtractor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ExtractDocumentContent implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $documentId)
    {
    }

    public int $tries = 2;
    public int $timeout = 120;

    public function handle(DocumentTextExtractor $extractor): void
    {
        $document = Document::find($this->documentId);
        if (!$document) {
            Log::warning('ExtractDocumentContent: document not found.', [
                'document_id' => $this->documentId,
            ]);
            return;
        }

        if (!empty(trim((string) $document->content))) {
            return;
        }

        try {
            $text = $extractor->extractFromDocument($document);

            $document->update([
                'content' => $text,
            ]);

            Log::info('ExtractDocumentContent: content extracted.', [
                'document_id' => $document->id,
                'length' => mb_strlen($text),
            ]);
        } catch (\Throwable $e) {
            Log::error('ExtractDocumentContent failed.', [
                'document_id' => $document->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
