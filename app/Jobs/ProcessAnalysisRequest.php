<?php

namespace App\Jobs;

use App\Events\PlagiarismAnalysisCompleted;
use App\Models\Document;
use App\Models\TextAnalysis;
use App\Services\CorpusPlagiarismAnalyzer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessAnalysisRequest implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $textAnalysisId)
    {
    }

    public int $timeout = 10000;
    public int $tries = 2;

    public function handle(CorpusPlagiarismAnalyzer $analyzer): void
    {
        $analysis = TextAnalysis::with(['analysisRequest', 'document'])->find($this->textAnalysisId);
        if (!$analysis || !$analysis->analysisRequest || !$analysis->document) {
            Log::error('ProcessAnalysisRequest: analysis, request, or document is missing.', [
                'text_analysis_id' => $this->textAnalysisId,
            ]);
            return;
        }

        $analysisRequest = $analysis->analysisRequest;
        $document = $analysis->document;
        $targetText = trim((string) $document->content);

        if ($targetText === '') {
            $analysis->update([
                'status' => 'failed',
                'error_message' => 'Document content is empty. Extraction may have failed.',
            ]);
            $analysisRequest->update(['status' => 'pending']);
            return;
        }

        try {
            $analysis->update([
                'status' => 'in_progress',
                'error_message' => null,
            ]);

            $corpusDocuments = Document::query()
                ->whereHas('corpus', function ($query) use ($analysis) {
                    $query->where('corpus.id', $analysis->corpus_id);
                })
                ->whereKeyNot($document->id)
                ->whereNotNull('content')
                ->get(['id', 'name', 'content']);

            $result = $analyzer->analyze($targetText, $corpusDocuments);

            $similarities = $result['similarities'] ?? [];
            $excerptedText = $result['excerpted_text'] ?? [];
            $plagiarismPercentage = (float) ($result['plagiarism_percentage'] ?? 0);

            $updatePayload = [
                'similarities' => $similarities,
                'plagiarism_percentage' => $plagiarismPercentage,
                'status' => 'completed',
                'error_message' => null,
            ];

            if ($analyzer->supportsHighlightedTextColumn()) {
                $updatePayload['highlighted_text'] = $result['highlighted_text'] ?? null;
            }

            if ($analyzer->supportsExcerptedTextColumn()) {
                $updatePayload['excerpted_text'] = is_array($excerptedText) ? $excerptedText : [];
            }

            $analysis->update($updatePayload);

            $analysisRequest->update(['status' => 'processed']);
            $document->update(['has_been_analyzed' => true]);

            $recipientUserIds = array_values(array_unique(array_filter([
                $analysisRequest->approved_by,
                $analysisRequest->user_id,
            ])));

            PlagiarismAnalysisCompleted::dispatch($analysis->id, 'completed', $recipientUserIds, $analysisRequest->id);

            $this->sendCompletionMail(
                approvedByEmail: $analysisRequest->approvedBy?->email,
                approvedByName: $analysisRequest->approvedBy?->name,
                requesterEmail: $analysisRequest->user?->email,
                requesterName: $analysisRequest->user?->name,
                analysisId: $analysis->id
            );
        } catch (\Throwable $e) {
            Log::error('ProcessAnalysisRequest failed.', [
                'text_analysis_id' => $this->textAnalysisId,
                'error' => $e->getMessage(),
            ]);

            $analysis->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            $analysisRequest->update(['status' => 'pending']);

            $recipientUserIds = array_values(array_unique(array_filter([
                $analysisRequest->approved_by,
                $analysisRequest->user_id,
            ])));

            PlagiarismAnalysisCompleted::dispatch($analysis->id, 'failed', $recipientUserIds, $analysisRequest->id);
        }
    }

    private function sendCompletionMail(
        ?string $approvedByEmail,
        ?string $approvedByName,
        ?string $requesterEmail,
        ?string $requesterName,
        int $analysisId
    ): void {
        $recipients = array_values(array_unique(array_filter([
            $approvedByEmail,
            $requesterEmail,
        ])));

        if ($recipients === []) {
            Log::warning('Completion mail skipped: no recipient email.', [
                'analysis_id' => $analysisId,
            ]);
            return;
        }

        try {
            $displayName = $approvedByName ?: $requesterName ?: 'Utilisateur';

            Mail::raw(
                "Bonjour {$displayName}, l'analyse #{$analysisId} est terminee. Consultez les resultats dans l'application.",
                static function ($message) use ($recipients): void {
                    $message->to($recipients)->subject('Analyse de plagiat terminee');
                }
            );

            Log::info('Completion mail sent.', [
                'analysis_id' => $analysisId,
                'recipients' => $recipients,
            ]);
        } catch (\Throwable $mailError) {
            Log::warning('Unable to send completion mail.', [
                'recipients' => $recipients,
                'error' => $mailError->getMessage(),
            ]);
        }
    }
}
