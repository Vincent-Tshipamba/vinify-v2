<?php

use Livewire\Component;

new class extends Component {
};
?>

<!-- Testimonials (marquee) -->
<section id="testimonials-section" class="bg-neutral-900 mx-auto px-6 py-20 sm:py-20">
    <div class="mx-auto mb-12 max-w-5xl text-center">
        <h2 class="font-bold text-white text-3xl sm:text-4xl">
            Témoignages
        </h2>
        <p class="mt-4 text-neutral-400 text-lg">
            Des étudiants partagent les bénéfices d’analyser leur TFC, TFE ou mémoire avant dépôt.
        </p>
    </div>

    <div class="relative mx-auto w-full max-w-5xl overflow-hidden marquee-row">
        <div
            class="top-0 left-0 z-10 absolute bg-linear-to-r from-neutral-900 to-transparent w-20 h-full pointer-events-none">
        </div>
        <div class="flex pt-10 pb-5 min-w-[200%] transform-gpu marquee-inner" id="row1"></div>
        <div
            class="top-0 right-0 z-10 absolute bg-linear-to-l from-neutral-900 to-transparent w-20 md:w-40 h-full pointer-events-none">
        </div>
    </div>

    <div class="relative mx-auto mt-6 w-full max-w-5xl overflow-hidden marquee-row">
        <div
            class="top-0 left-0 z-10 absolute bg-linear-to-r from-neutral-900 to-transparent w-20 h-full pointer-events-none">
        </div>
        <div class="flex pt-5 pb-10 min-w-[200%] transform-gpu marquee-inner marquee-reverse" id="row2"></div>
        <div
            class="top-0 right-0 z-10 absolute bg-linear-to-l from-neutral-900 to-transparent w-20 md:w-40 h-full pointer-events-none">
        </div>
    </div>

    <div class="mx-auto mt-8 max-w-5xl text-center">
        <a href="{{ route('analysis.request') }}"
            class="inline-flex items-center gap-x-2 bg-[#ff0] px-3 py-2 rounded-full font-medium text-neutral-800 text-sm"
            wire:navigate>
            <span>Demander une analyse</span>
            <span class="hidden text-xs" wire:loading.delay>Chargement...</span>
        </a>
    </div>
</section>

@push('scripts')
    <script>
        // Données de témoignages (inlinées)
        const testimonialsData = [
            {
                name: 'Rebecca M.',
                role: 'Étudiante en Génie Logiciel',
                testimonial: "Mon TFC contenait des passages trop proches des sources. Vinify m’a guidé pour reformuler et citer correctement avant le dépôt.",
                university: 'ISIPA Kinshasa'
            },
            {
                name: 'Junior K.',
                role: 'Étudiant en Réseaux',
                testimonial: "Avec Vinify, j’ai pu vérifier mon TFE en 24h. J’étais rassuré de connaître le taux de similitude exact.",
                university: 'ISIPA Kinshasa'
            },
            {
                name: 'Nadine B.',
                role: 'Étudiante en Systèmes Informatiques',
                testimonial: "Le rapport d’analyse m’a montré les passages sensibles et les sources. J’ai corrigé avant la soutenance.",
                university: 'ISIPA Kinshasa'
            },
            {
                name: 'Cedrick L.',
                role: 'Étudiant en Informatique',
                testimonial: "Je voulais éviter tout problème de plagiat pour mon mémoire. Vinify a été clair et rapide.",
                university: 'ISIPA Kinshasa'
            },
            {
                name: 'Sandrine P.',
                role: 'Étudiante en Data',
                testimonial: "La transparence du process m’a rassurée. Je savais exactement comment l’analyse était faite.",
                university: 'ISIPA Kinshasa'
            },
            {
                name: 'Hervé T.',
                role: 'Étudiant en Développement',
                testimonial: "Vinify m’a aidé à soumettre un travail original et bien cité. Un vrai gain de confiance.",
                university: 'ISIPA Kinshasa'
            }
        ];

        const cardsData = testimonialsData.map((testimonial, index) => ({
            ...testimonial,
            image: [
                'https://images.unsplash.com/photo-1633332755192-727a05c4013d?q=80&w=200',
                'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=200',
                'https://images.unsplash.com/photo-1527980965255-d3b416303d12?w=200&auto=format&fit=crop&q=60',
                'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=200&auto=format&fit=crop&q=60'
            ][index % 4],
            handle: `@${testimonial.name.toLowerCase().replace(/\s+/g, '')}`
        }));

        const row1 = document.getElementById('row1');
        const row2 = document.getElementById('row2');

        const createCard = (card) => `
                <div class="bg-neutral-800 shadow hover:shadow-lg dark:shadow-slate-700 mx-4 p-4 rounded-lg w-72 transition-all duration-200 shrink-0">
                    <div class="flex gap-2">
                        <img class="rounded-full size-11" src="${card.image}" alt="${card.name}">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1">
                                <p class="text-white text-sm">${card.name}</p>
                            </div>
                            <span class="text-neutral-400 text-xs">${card.role} • ${card.university}</span>
                        </div>
                    </div>
                    <p class="pt-4 text-neutral-300 text-sm">${card.testimonial}</p>
                </div>
            `;

        const renderCards = (target) => {
            const doubled = [...cardsData, ...cardsData];
            doubled.forEach(card => target.insertAdjacentHTML('beforeend', createCard(card)));
        };

        renderCards(row1);
        renderCards(row2);

        // Simple marquee animation via CSS variables (kept minimal to match existing style)
        document.querySelectorAll('.marquee-inner').forEach((el, i) => {
            el.style.display = 'flex';
            el.style.gap = '1rem';
            el.style.animation = `marquee ${20 + i * 6}s linear infinite`;
        });
    </script>
    <script>
        const makeSlider = (container) => {
            let isDown = false;
            let startX;
            let scrollLeft;

            // Pause animation on interaction
            const pauseAnimation = () => container.style.animationPlayState = 'paused';
            const resumeAnimation = () => container.style.animationPlayState = 'running';

            container.addEventListener('mousedown', (e) => {
                isDown = true;
                container.classList.add('cursor-grabbing');
                pauseAnimation();

                startX = e.pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
            });

            container.addEventListener('mouseleave', () => {
                if (isDown) resumeAnimation();
                isDown = false;
            });

            container.addEventListener('mouseup', () => {
                isDown = false;
                resumeAnimation();
            });

            container.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - container.offsetLeft;
                const walk = (x - startX) * 1.5; // speed
                container.scrollLeft = scrollLeft - walk;
            });

            // Mobile touch support
            container.addEventListener('touchstart', (e) => {
                isDown = true;
                pauseAnimation();
                startX = e.touches[0].pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
            });

            container.addEventListener('touchend', () => {
                isDown = false;
                resumeAnimation();
            });

            container.addEventListener('touchmove', (e) => {
                if (!isDown) return;
                const x = e.touches[0].pageX - container.offsetLeft;
                const walk = (x - startX) * 1.4;
                container.scrollLeft = scrollLeft - walk;
            });
        };

        // Apply to both rows
        makeSlider(document.getElementById('row1'));
        makeSlider(document.getElementById('row2'));
    </script>
@endpush
