<?php

use Livewire\Component;
use App\Models\AnalysisRequest;

new class extends Component {
    public AnalysisRequest $analysisRequest;

    public function mount(AnalysisRequest $analysisRequest)
    {
        $this->analysisRequest = $analysisRequest;
    }

};
?>

<div>
    @php
$analysis = $analysisRequest->analysis;
$score = (int) round($analysis?->plagiarism_percentage ?? 0);
$segments = is_array($analysis?->similarities) ? count($analysis->similarities) : 0;
$riskLabel = $score < 15 ? 'Low Risk' : ($score < 35 ? 'Moderate Risk' : 'High Risk');
$riskClass =
    $score < 15
    ? 'bg-green-500/15 text-green-400'
    : ($score < 35
        ? 'bg-orange-500/15 text-orange-400'
        : 'bg-red-500/15 text-red-400');
    @endphp

    <style>
        .plagiarized {
            color: #f0bd02;
            font-weight: 700;
            cursor: pointer;
            text-decoration: underline;
            text-decoration-style: dashed;
            text-underline-offset: 2px;
        }
    </style>

    <div class="flex sm:flex-row flex-col sm:justify-between sm:items-center gap-y-4 mb-6 page-breadcrumb">
        <nav
            class="flex items-center gap-x-2 bg-white/10 dark:bg-neutral-800/50 shadow-sm backdrop-blur-md px-6 py-3 rounded-xl text-gray-500 dark:text-gray-400 text-sm">
            <a href="{{ route('dashboard') }}" wire:navigate
                class="inline-flex items-center gap-x-1 font-medium hover:text-gray-700 dark:hover:text-white">
                <span>Dashboard</span>
            </a>
            <span>/</span>
            <a href="{{ route('requests.index') }}" wire:navigate
                class="inline-flex items-center gap-x-1 font-medium hover:text-gray-700 dark:hover:text-white">
                <span>Demandes d'analyses</span>
            </a>
            <span>/</span>
            <span class="font-semibold text-yellow-400 dark:text-yellow-400">Demande de {{ $analysisRequest->user->name }}</span>
        </nav>

        <div>
            <a href="{{ route('requests.progress', $analysisRequest) }}" wire:navigate
                class="inline-flex items-center bg-[#f0bd02] hover:bg-[#e2b616] px-4 py-3 rounded-lg font-semibold text-gray-800">
                Lancer l'analyse
            </a>
        </div>
    </div>

    <div class="mb-6">
        <h2 class="font-bold text-white text-2xl">Analysis Results</h2>
        <p class="mt-1 text-gray-400 text-lg">Detailed plagiarism report for: <span class="font-semibold text-white">{{ $analysisRequest->document->name }}</span></p>
    </div>

    @if (!$analysis)
        <div class="bg-white/10 dark:bg-neutral-900 p-6 border border-neutral-700 rounded-xl">
            <p class="text-gray-200 text-sm">Aucune analyse disponible pour cette demande.</p>
            <a href="{{ route('requests.progress', $analysisRequest) }}" wire:navigate
                class="inline-flex items-center bg-[#f0bd02] hover:bg-[#e2b616] mt-4 px-4 py-2 rounded-lg font-semibold text-gray-900">
                Ouvrir la progression
            </a>
        </div>
    @else
        <div class="gap-6 grid grid-cols-1 lg:grid-cols-3">
            <aside class="space-y-6 lg:col-span-1">
                <section class="bg-white/10 dark:bg-neutral-900 p-6 border border-neutral-700 rounded-xl">
                    <h3 class="mb-4 font-semibold text-gray-200 text-xl">Overall Score</h3>
                    <div class="flex justify-center items-center">
                        <div class="relative flex justify-center items-center rounded-full w-52 h-52"
                            style="background: conic-gradient(#4f46e5 {{ $score }}%, #27272a {{ $score }}%);">
                            <div class="absolute bg-zinc-100 dark:bg-zinc-900 rounded-full w-40 h-40"></div>
                            <div class="absolute font-bold text-[#4f46e5] dark:text-[#818cf8] text-6xl">{{ $score }}%</div>
                        </div>
                    </div>
                    <div class="flex justify-center mt-5">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $riskClass }}">{{ $riskLabel }}</span>
                    </div>
                </section>

                <section class="bg-white/10 dark:bg-neutral-900 p-6 border border-neutral-700 rounded-xl">
                    <h3 class="font-semibold text-gray-200 text-xl">Flagged Segments</h3>
                    <div class="mt-3 font-bold text-white text-3xl">{{ $segments }}</div>
                    <p class="mt-3 text-gray-400 text-sm">Passages with high similarity to existing sources.</p>
                </section>
            </aside>

            <main class="lg:col-span-2 bg-white/10 dark:bg-neutral-900 p-6 border border-neutral-700 rounded-xl">
                <h3 class="mb-4 font-bold text-white text-xl">Document Content</h3>

                <div class="bg-zinc-100/80 dark:bg-zinc-900/70 p-5 border border-zinc-200/70 dark:border-zinc-700 rounded-xl max-h-[70vh] overflow-auto text-black dark:text-gray-100 leading-8 whitespace-pre-wrap"
                    id="scanned-document-content">
                    {!! $analysis->highlighted_text ?: nl2br(e($analysisRequest->document->content ?? 'Aucun contenu disponible.')) !!}
                </div>

                <details class="group bg-zinc-100/40 dark:bg-zinc-900/40 mt-6 p-4 border border-zinc-200/70 dark:border-zinc-700 rounded-xl">
                    <summary class="text-gray-800 dark:text-gray-200 cursor-pointer">Apercu du document original</summary>
                    <div class="mt-4">
                        <livewire:preview-document :documentName="$analysisRequest->document->name"
                            :fileUrl="$analysisRequest->document->file_url"
                            :fileExtension="$analysisRequest->document->file_original_extension" />
                    </div>
                </details>
            </main>
        </div>
    @endif
</div>

<script>
    window.analysisSimilarities = @json($analysisRequest->analysis->similarities ?? []);
</script>
<script>
    function bindAnalysisExcerptModal() {
        document.removeEventListener('click', handleExcerptClick);
        document.addEventListener('click', handleExcerptClick);
    }

    function handleExcerptClick(event) {
        const target = event.target.closest('.plagiarized');
        if (!target) return;

        const excerptId = String(target.dataset.id || '');
        if (!excerptId) return;

        const item = (window.analysisSimilarities || []).find(function (entry) {
            return String(entry.id) === excerptId;
        });
        if (!item) return;

        Swal.fire({
            title: "Detail du plagiat",
            html: `
                    <div class="space-y-3 text-start">
                        <p><strong class="font-bold italic">Texte plagié :</strong><br>${escapeHtml(item.plagiarized_text || '')}</p>
                        <p><strong class="font-bold italic">Texte source :</strong><br>${escapeHtml(item.source_phrase || '')}</p>
                        <p><strong class="font-bold italic">Document source :</strong> ${escapeHtml(item.source_document_name || ('#' + (item.source_document_id || 'N/A')))}</p>
                        <p><strong class="font-bold italic">Similarite :</strong> <span style="color:#f0bd02;font-weight:700">${Number(item.similarity_percentage || 0).toFixed(2)}%</span></p>
                    </div>
                `,
            icon: "info",
            theme: 'dark',
            color: '#ffffff',
            background: '#000000',
            showClass: {
                popup: 'swal2-show',
                backdrop: 'swal2-backdrop-show',
                icon: 'swal2-icon-show'
            },
            hideClass: {
                popup: 'swal2-hide',
                backdrop: 'swal2-backdrop-hide',
                icon: 'swal2-icon-hide'
            },
            customClass: {
                popup: 'bg-gray-200 dark:bg-gray-900 text-black dark:text-gray-50 rounded-lg shadow-lg',
                confirmButton: 'bg-[#f0bd02] text-black font-bold py-2 px-4 rounded',
            },
        });
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
    bindAnalysisExcerptModal();

    document.addEventListener('DOMContentLoaded', bindAnalysisExcerptModal);
    document.addEventListener('livewire:navigated', bindAnalysisExcerptModal);
</script>
