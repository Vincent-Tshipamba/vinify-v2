<?php

use Livewire\Component;

new class extends Component
{
};
?>

<!-- About -->
<section id="about" class="bg-neutral-900">
    <div class="mx-auto px-4 xl:px-0 py-10 lg:py-20 max-w-5xl">
        <div class="items-center gap-10 grid grid-cols-1 md:grid-cols-2">
            <div>
                <h2 class="font-semibold text-white text-2xl md:text-4xl md:leading-tight">A propos</h2>
                <p class="mt-4 text-neutral-400 text-base md:text-lg">
                    Vinify est porté par le Centre de Compétences de l'Institut Supérieur d'Informatique,
                    Programmation et Analyse (ISIPA). Cette structure accompagne l'innovation pédagogique et
                    technologique, en mettant la rigueur scientifique au service de la qualité des travaux académiques.
                </p>
                <p class="mt-4 text-neutral-400 text-base md:text-lg">
                    Notre ambition est simple : offrir aux étudiants un outil fiable pour s'assurer de l'originalité
                    de leurs TFC, TFE et mémoires, dans un esprit de transparence et de respect des sources.
                </p>
            </div>
            <div class="hidden md:flex justify-center">
                <img class="w-52 lg:w-64" src="{{ asset('img/vinify.png') }}" alt="Vinify">
            </div>
        </div>
    </div>
</section>
<!-- End About -->
