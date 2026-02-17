<?php

use Livewire\Component;

new class extends Component {
    public string $documentName;
    public $fileUrl;
    public $fileExtension;

    public function mount($documentName, $fileUrl, $fileExtension)
    {
        $this->documentName = $documentName;
        $this->fileUrl = $fileUrl;
        $this->fileExtension = $fileExtension;
    }
};
?>

<div>
    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($fileUrl)
        <h2 class="mb-4 font-bold text-xl">Nom du document : <span class="text-white">{{ $documentName }}</span></h2>

        @if($fileExtension === 'pdf')
            <iframe src="{{ asset('storage/' . $fileUrl) }}" class="w-full h-screen" frameborder="0"></iframe>

        @elseif ($fileExtension === 'docx')
            <div id="docx-preview" class="bg-white p-6 rounded w-full h-screen overflow-auto text-black">
            </div>
        @else
            <p>Format de document non pris en charge.</p>
        @endif
    @else
        <p>Aucun document disponible pour l'affichage.</p>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', loadDocx);
    document.addEventListener('livewire:navigated', loadDocx);

    async function loadDocx() {
        const container = document.getElementById('docx-preview');
        if (!container || !window.docx) return;

        try {
            const response = await fetch("/storage/" + $wire.fileUrl);

            if (!response.ok) throw new Error('Failed to fetch docx file');

            const buffer = await response.arrayBuffer();
            await window.docx.renderAsync(buffer, container, null, {
                className: "docx",
                ignoreWidth: false,
                ignoreHeight: false,
            });
        } catch (err) {
            container.innerHTML = `<p class="text-red-500">Impossible de prévisualiser le document : ${err.message}</p>`;
            console.error(err);
        }
    }
    loadDocx();
</script>
