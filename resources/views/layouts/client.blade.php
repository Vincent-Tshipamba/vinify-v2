<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Vinify' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        const html = document.querySelector('html');
        const isLightOrAuto = localStorage.getItem('hs_theme') === 'light' || (localStorage.getItem('hs_theme') ===
            'auto' && !window.matchMedia('(prefers-color-scheme: dark)').matches);
        const isDarkOrAuto = localStorage.getItem('hs_theme') === 'dark' || (localStorage.getItem('hs_theme') === 'auto' &&
            window.matchMedia('(prefers-color-scheme: dark)').matches);

        if (isLightOrAuto && html.classList.contains('dark')) html.classList.remove('dark');
        else if (isDarkOrAuto && html.classList.contains('light')) html.classList.remove('light');
        else if (isDarkOrAuto && !html.classList.contains('dark')) html.classList.add('dark');
        else if (isLightOrAuto && !html.classList.contains('light')) html.classList.add('light');
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .marquee-reverse {
            animation-direction: reverse;
        }
    </style>
    <style>
        html {
            scroll-behavior: smooth;
        }

        .wave-divider {
            position: relative;
            top: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
        }

        .wave-divider svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 60px;
        }

        @media (min-width: 768px) {
            .wave-divider svg {
                height: 150px;
            }
        }
    </style>
    @livewireStyles
</head>

<body class="bg-neutral-900">
    <div id="page-content" class="min-h-screen transition duration-150">
        <livewire:client.navbar />
        {{ $slot }}

        <livewire:client.footer />
    </div>

    @livewireScripts
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/preline/dist/index.js"></script>

    <script src="https://unpkg.com/mammoth/mammoth.browser.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/jszip/dist/jszip.min.js"></script>
    <script src="https://unpkg.com/docx-preview/dist/docx-preview.js"></script>
    <script>
        function initDocumentPreview() {
            const input = document.getElementById('document-input');
            const btn = document.getElementById('preview-document');

            if (!input || !btn) return;

            let selectedFile = null;

            input.addEventListener('change', () => {
                selectedFile = input.files && input.files[0] ? input.files[0] : null;
                btn.disabled = !selectedFile;
            });

            btn.addEventListener('click', async () => {
                if (!selectedFile) return;

                const ext = (selectedFile.name.split('.').pop() || '').toLowerCase();

                if (ext === 'pdf') {
                    const url = URL.createObjectURL(selectedFile);
                    Swal.fire({
                        title: 'Aperçu du document',
                        html: `<iframe src="${url}" style="width:100%;height:70vh;border:none;"></iframe>`,
                        showCloseButton: true,
                        showConfirmButton: false,
                        didClose: () => URL.revokeObjectURL(url)
                    });
                    return;
                }

                if (ext === 'docx') {
                    const arrayBuffer = await selectedFile.arrayBuffer();
                    const containerId = 'docx-preview-container';
                    Swal.fire({
                        title: 'Aperçu du document',
                        // width: '70%',
                        html: `<div id="${containerId}" style="width:100%;max-height:70vh;overflow:auto;"></div>`,
                        showCloseButton: true,
                        showConfirmButton: false,
                        didOpen: async () => {
                            const container = document.getElementById(containerId);
                            if (container && window.docx) {
                                await window.docx.renderAsync(arrayBuffer, container, null, {
                                    inWrapper: false,
                                    renderFootnotes: true,
                                    ignoreWidth: false,
                                    ignoreHeight: false,
                                    breakPages: true,
                                    ignoreLastRenderedPageBreak: true,
                                    renderFooters: true,
                                    useBase64URL: true,
                                });
                            }
                        }
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', initDocumentPreview);
        document.addEventListener('livewire:navigated', initDocumentPreview);
    </script>
</body>

</html>