<?php

use App\Jobs\ProcessAnalysisRequest;
use App\Models\AnalysisRequest;
use App\Models\Corpus;
use App\Models\TextAnalysis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

new class extends Component {
    public AnalysisRequest $analysisRequest;
    public ?TextAnalysis $analysis = null;
    public string $selectedCorpus = 'GLOBAL';
    public int $progress = 0;
    public string $stepOneStatus = 'pending';
    public string $stepTwoStatus = 'pending';
    public string $stepThreeStatus = 'pending';
    public ?string $errorMessage = null;
    public bool $hasStarted = false;

    public function mount(AnalysisRequest $analysisRequest): void
    {
        $this->analysisRequest = $analysisRequest->load('document', 'analysis', 'university');
        $this->analysis = $this->analysisRequest->analysis;

        if (!$this->analysisRequest->university_id) {
            $this->selectedCorpus = 'GLOBAL';
        } elseif ($this->analysis && $this->analysis->corpus) {
            $this->selectedCorpus = $this->analysis->corpus->type;
        }

        $this->refreshProgress();
    }

    public function startAnalysis(): void
    {
        $this->validate([
            'selectedCorpus' => 'required|in:GLOBAL,UNIVERSITY',
        ]);

        if ($this->selectedCorpus === 'UNIVERSITY' && !$this->analysisRequest->university_id) {
            $this->addError('selectedCorpus', 'Aucun corpus universitaire disponible pour cette demande.');
            return;
        }

        try {
            DB::transaction(function (): void {
                $lockedRequest = AnalysisRequest::query()
                    ->with('document')
                    ->lockForUpdate()
                    ->findOrFail($this->analysisRequest->id);

                $document = $lockedRequest->document()->firstOrFail();
                $content = trim((string) $document->content);
                if ($content === '') {
                    throw new \RuntimeException(
                        'Le contenu du document n\'est pas encore disponible. Patientez la fin de l\'extraction automatique puis relancez.'
                    );
                }

                $lockedRequest->update([
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'status' => 'in_progress',
                ]);

                $corpus = $this->selectedCorpus === 'UNIVERSITY'
                    ? Corpus::firstOrCreate([
                        'type' => 'UNIVERSITY',
                        'university_id' => $lockedRequest->university_id,
                    ])
                    : Corpus::firstOrCreate([
                        'type' => 'GLOBAL',
                        'university_id' => null,
                    ]);

                $document->corpus()->syncWithoutDetaching([$corpus->id]);

                $analysis = TextAnalysis::query()->updateOrCreate(
                    ['analysis_request_id' => $lockedRequest->id],
                    [
                        'user_id' => $lockedRequest->user_id,
                        'document_id' => $lockedRequest->document_id,
                        'corpus_id' => $corpus->id,
                        'status' => 'in_progress',
                        'error_message' => null,
                    ]
                );

                ProcessAnalysisRequest::dispatch($analysis->id);
            });

            $this->hasStarted = true;
            $this->refreshProgress();
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function refreshProgress(): void
    {
        $this->analysisRequest = AnalysisRequest::query()
            ->with('analysis', 'document')
            ->findOrFail($this->analysisRequest->id);
        $this->analysis = $this->analysisRequest->analysis;

        $stepOneDone = !empty(trim((string) $this->analysisRequest->document?->content));
        $this->stepOneStatus = $stepOneDone ? 'completed' : 'pending';

        $analysisStatus = $this->analysis?->status ?? 'pending';
        $this->stepTwoStatus = match ($analysisStatus) {
            'completed' => 'completed',
            'failed' => 'failed',
            'in_progress', 'processing' => 'in_progress',
            default => 'pending',
        };

        $isProcessed = $this->analysisRequest->status === 'processed';
        $this->stepThreeStatus = ($this->stepTwoStatus === 'completed' && $isProcessed) ? 'completed' : 'pending';

        if ($this->stepOneStatus === 'pending') {
            $this->progress = 0;
        } elseif ($this->stepTwoStatus === 'pending') {
            $this->progress = 33;
        } elseif ($this->stepTwoStatus === 'in_progress') {
            $this->progress = 67;
        } elseif ($this->stepTwoStatus === 'failed') {
            $this->progress = 67;
        } elseif ($this->stepThreeStatus === 'completed') {
            $this->progress = 100;
        } else {
            $this->progress = 90;
        }

        $this->errorMessage = $this->stepTwoStatus === 'failed'
            ? ($this->analysis?->error_message ?? 'Une erreur est survenue pendant l\'analyse.')
            : null;

        $analysisStatus = $this->analysis?->status;

        $this->hasStarted =
            in_array($this->analysisRequest->status, ['in_progress', 'processed'], true) ||
            in_array((string) $analysisStatus, ['in_progress', 'processing', 'completed', 'failed'], true);
    }

    public function render()
    {
        return $this->view()->layout('layouts::app');
    }
};
?>

<div class="space-y-6" wire:poll="refreshProgress">
    <div class="flex sm:flex-row flex-col sm:justify-between sm:items-center gap-y-4 page-breadcrumb">
        <nav
            class="flex items-center gap-x-2 bg-white/10 dark:bg-neutral-800/50 shadow-sm backdrop-blur-md px-6 py-3 rounded-xl text-gray-500 dark:text-gray-400 text-sm">
            <a href="{{ route('dashboard') }}" wire:navigate
                class="inline-flex items-center gap-x-1 font-medium hover:text-gray-700 dark:hover:text-white">
                <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-7-7v18" />
                </svg>
                <span>Dashboard</span>
            </a>
            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4
                                            4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('requests.index') }}" wire:navigate
                class="inline-flex items-center gap-x-1 font-medium hover:text-gray-700 dark:hover:text-white">
                <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-7-7v18" />
                </svg>
                <span>Demandes d'analyses</span>
            </a>
            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4
                                            4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('requests.show', $analysisRequest->id) }}" wire:navigate
                class="inline-flex items-center gap-x-1 font-medium hover:text-gray-700 dark:hover:text-white">
                <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-7-7v18" />
                </svg>
                <span>Demande de {{ $analysisRequest->user->name }}</span>
            </a>
            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4
                4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-semibold text-yellow-400 dark:text-yellow-400">Progression de l'analyse</span>
        </nav>
    </div>

    <div class="bg-white/10 dark:bg-neutral-900 p-6 border border-neutral-700 rounded-xl">
        <div id="analysis-progress-root" data-progress="{{ $progress }}" data-analysis-id="{{ $analysis?->id }}">
            <div class="flex justify-between items-center mb-3 text-sm">
                <span class="text-gray-200">Progression</span>
                <span class="font-semibold text-[#f0bd02]">{{ $progress }}%</span>
            </div>
            <div class="bg-neutral-800 rounded-full h-3 overflow-hidden">
                <div class="bg-[#f0bd02] h-3 transition-all duration-500" style="width: {{ $progress }}%;"></div>
            </div>
        </div>

        <div class="gap-4 grid grid-cols-1 md:grid-cols-3 mt-6">
            <div class="p-4 border rounded-lg {{ $stepOneStatus === 'completed' ? 'border-green-500 bg-green-900/20' : 'border-neutral-700 bg-neutral-800/30' }}">
                <p class="font-semibold text-sm">1. Extraction de contenu</p>
                <p class="mt-1 text-xs">{{ $stepOneStatus === 'completed' ? 'Terminée' : 'En attente' }}</p>
            </div>
            <div class="p-4 border rounded-lg
                {{ $stepTwoStatus === 'completed' ? 'border-green-500 bg-green-900/20' : '' }}
                {{ $stepTwoStatus === 'in_progress' ? 'border-yellow-500 bg-yellow-900/20' : '' }}
                {{ $stepTwoStatus === 'failed' ? 'border-red-500 bg-red-900/20' : '' }}
                {{ $stepTwoStatus === 'pending' ? 'border-neutral-700 bg-neutral-800/30' : '' }}">
                <p class="font-semibold text-sm">2. Analyse du document</p>
                <p class="mt-1 text-xs">
                    @if ($stepTwoStatus === 'completed')
                        Terminée
                    @elseif ($stepTwoStatus === 'in_progress')
                        En cours
                    @elseif ($stepTwoStatus === 'failed')
                        Échouée
                    @else
                        En attente
                    @endif
                </p>
            </div>
            <div class="p-4 border rounded-lg {{ $stepThreeStatus === 'completed' ? 'border-green-500 bg-green-900/20' : 'border-neutral-700 bg-neutral-800/30' }}">
                <p class="font-semibold text-sm">3. Exportation des resultats</p>
                <p class="mt-1 text-xs">{{ $stepThreeStatus === 'completed' ? 'Terminée' : 'En attente' }}</p>
            </div>
        </div>
    </div>

    @if (!$hasStarted)
        <div class="bg-white/10 dark:bg-neutral-900 p-6 border border-neutral-700 rounded-xl">
            <h3 class="font-semibold text-lg">Choix du corpus</h3>
            <p class="mt-1 text-gray-400 text-sm">Choisissez le corpus à utiliser pour lancer l'analyse.</p>

            <div class="gap-4 grid grid-cols-1 md:grid-cols-2 mt-5">
                <label class="cursor-pointer">
                    <input type="radio" wire:model="selectedCorpus" value="GLOBAL" class="peer hidden">
                    <div class="bg-neutral-800/50 p-4 border border-neutral-700 peer-checked:border-[#f0bd02] rounded-lg">
                        <p class="font-semibold">Corpus global</p>
                        <p class="mt-1 text-gray-400 text-xs">Compare le document avec l'ensemble des sources globales.</p>
                    </div>
                </label>

                <label class="cursor-pointer {{ !$analysisRequest->university_id ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <input type="radio" wire:model="selectedCorpus" value="UNIVERSITY" class="peer hidden"
                        {{ !$analysisRequest->university_id ? 'disabled' : '' }}>
                    <div class="bg-neutral-800/50 p-4 border border-neutral-700 peer-checked:border-[#f0bd02] rounded-lg">
                        <p class="font-semibold">Corpus université</p>
                        <p class="mt-1 text-gray-400 text-xs">
                            @if ($analysisRequest->university_id)
                                Compare avec les documents de l'université.
                            @else
                                Aucun corpus université disponible pour cette demande.
                            @endif
                        </p>
                    </div>
                </label>
            </div>

            @error('selectedCorpus')
                <p class="mt-3 text-red-400 text-sm">{{ $message }}</p>
            @enderror

            <div class="mt-6">
                <button type="button" wire:click="startAnalysis"
                    class="inline-flex items-center bg-[#f0bd02] hover:bg-[#e2b616] px-4 py-2 rounded-lg font-semibold text-gray-900">
                    Lancer l'analyse
                </button>
            </div>
        </div>
    @endif

    @if ($hasStarted)
        <div class="bg-white/10 dark:bg-neutral-900 p-6 border border-neutral-700 rounded-xl">
            <p class="text-sm">
                Vous pouvez quitter cette page. Une notification navigateur et un email seront envoyés quand l'analyse sera terminée.
            </p>

            @if ($stepThreeStatus === 'completed' && $analysis)
                <a href="{{ route('requests.show', $analysisRequest->id) }}"
                    class="inline-flex items-center bg-[#f0bd02] hover:bg-[#e2b616] mt-4 px-4 py-2 rounded-lg font-semibold text-gray-900">
                    Voir les résultats
                </a>
            @endif
        </div>
    @endif

    @if ($errorMessage)
        <div class="bg-red-500/15 p-4 border border-red-500 rounded-lg text-red-200 text-sm">
            {{ $errorMessage }}
        </div>
    @endif

</div>
