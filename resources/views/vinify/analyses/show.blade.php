<x-app-layout>
    <style>
        .plagiarized {
            background-color: #fbbf24;
            /* jaune */
            color: black;
            font-weight: bold;
            cursor: pointer;
            padding: 1px 3px;
            border-radius: 3px;
            text-decoration: underline;
        }
    </style>

    <div class="md:flex">
        <ul id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist"
            data-tabs-active-classes="dark:text-[#ff0] font-bold"
            class="flex-column space-y space-y-4 text-sm font-medium text-gray-500 dark:text-gray-400 md:me-4 mb-4 md:mb-0 w-full md:w-1/4">
            <li role="presentation">
                <button type="button" id="preview-document-tab" data-tabs-target="#preview-document" type="button"
                    role="tab" aria-controls="preview-document" aria-selected="true"
                    class="inline-flex items-center px-4 py-3 text-white dark:text-gray-400 bg-gray-900 rounded-lg active w-full dark:bg-gray-900">
                    Aperçu du document
                </button>
            </li>
            <li role="presentation">
                <button type="button" id="scanned-document-tab" data-tabs-target="#scanned-document" type="button"
                    role="tab" aria-controls="scanned-document" aria-selected="false"
                    class="inline-flex items-center px-4 py-3 text-white dark:text-gray-400 bg-gray-900 rounded-lg active w-full dark:bg-gray-900">
                    Document analysé
                </button>
            </li>
            <li role="presentation">
                <button type="button" id="excerpted-tab" data-tabs-target="#excerpted" type="button" role="tab"
                    aria-controls="excerpted" aria-selected="false"
                    class="inline-flex items-center px-4 py-3 text-white bg-gray-900 rounded-lg active w-full dark:bg-gray-900">
                    Extraits plagiés
                </button>
            </li>
            <li role="presentation">
                <button type="button" id="percentage-tab" data-tabs-target="#percentage" type="button" role="tab"
                    aria-controls="percentage" aria-selected="false"
                    class="inline-flex items-center px-4 py-3 text-white bg-gray-900 rounded-lg active w-full dark:bg-gray-900">
                    Pourcentage de plagiat
                </button>
            </li>
        </ul>
        <div id="default-tab-content"
            class="p-4 bg-gray-50 text-medium text-gray-500 dark:text-gray-400 dark:bg-gray-900 rounded-lg w-full">
            <div class="hidden p-4 h-screen rounded-lg bg-gray-50 dark:bg-gray-900" id="preview-document"
                role="tabpanel" aria-labelledby="preview-document-tab">
                @livewire('preview-document', ['textAnalysisId' => $textAnalysis->id])
            </div>
            <div class="hidden p-4 overflow-auto whitespace-pre-wrap rounded-lg bg-gray-50 dark:bg-gray-900 dark:text-gray-50"
                id="scanned-document" role="tabpanel" aria-labelledby="scanned-document-tab">
                {!! $textAnalysis->highlighted_text !!}
            </div>
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-900" id="excerpted" role="tabpanel"
                aria-labelledby="excerpted-tab">
                @livewire('show-excerpted-text', ['textAnalysisId' => $textAnalysis->id])
            </div>
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-900" id="percentage" role="tabpanel"
                aria-labelledby="percentage-tab">
                @livewire('show-plagiarism-percentage', ['textAnalysisId' => $textAnalysis->id])
            </div>
        </div>
    </div>


    <script>
        window.similaritiesList = @json($similaritiesList);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.querySelector('pre') || document; // ou un div parent
            console.log(container);
            container.addEventListener('click', e => {
                const span = e.target.closest('span.plagiarized');
                console.log(span);
                if (!span) return;

                const id = span.dataset.id;
                console.log(id)
                const data = window.similaritiesList?.find(item => item.id == id);
                console.log(data);
                if (!data) return;

                showModal(data);
            });
        });

        function showModal(data) {
            Swal.fire({
                title: "Détail du plagiat",
                html: `
                    <div class="p-1 rounded-lg text-start">
                        <p class="mb-2"><strong>Phrase :</strong> ${data.plagiarized_text}</p>
                        <p class="mb-2"><strong>Similarité :</strong><span class="dark:text-[#ff0]"> ${data.similarity_percentage}%</span></p>
                        <p class="mb-2 text-start" title="Cliquez pour suivre le lien de la source..."><strong>Source :</strong> <a href="${data.link}" target="_blank" class="underline" title="Cliquez pour suivre le lien de la source...">${data.title}</a></p>
                    </div>
                `,
                icon: "info",
                customClass: {
                    popup: 'bg-gray-200 dark:bg-gray-900 text-start text-black dark:text-gray-50 rounded-lg shadow-lg',
                    confirmButton: 'dark:bg-[#ff0] dark:text-black font-bold py-2 px-4 rounded',
                },
            });
        }
    </script>

</x-app-layout>
