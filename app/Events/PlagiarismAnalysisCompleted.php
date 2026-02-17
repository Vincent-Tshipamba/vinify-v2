<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlagiarismAnalysisCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $textAnalysisId;
    public $analysisRequestId;
    public $status;
    public $recipientUserIds;
    /**
     * Create a new event instance.
     */
    public function __construct(int $textAnalysisId, string $status, array $recipientUserIds = [], ?int $analysisRequestId = null)
    {
        $this->textAnalysisId = $textAnalysisId;
        $this->analysisRequestId = $analysisRequestId;
        $this->status = $status;
        $this->recipientUserIds = array_values(array_unique(array_filter($recipientUserIds)));
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new Channel("plagiarism-analysis.$this->textAnalysisId"),
        ];

        foreach ($this->recipientUserIds as $userId) {
            $channels[] = new Channel("plagiarism-user.$userId");
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'analysis-completed';
    }
}
