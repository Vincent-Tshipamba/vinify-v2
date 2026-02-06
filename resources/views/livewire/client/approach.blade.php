<?php

use Livewire\Component;

new class extends Component
{
};
?>

<!-- Approach -->
<div class="bg-neutral-900" id="approach">
    <div class="mx-auto px-4 xl:px-0 py-10 lg:pt-20 max-w-5xl">
        <!-- Title -->
        <div class="mb-10 lg:mb-14 max-w-3xl">
            <h2 class="font-semibold text-white text-2xl md:text-4xl md:leading-tight">Comment fonctionne <span
                    class="text-[#ff0]">Vinify </span>?</h2>
            <p class="mt-1 text-neutral-400">
                Nous expliquons chaque étape de manière claire pour que vous sachiez exactement comment votre TFC, TFE,
                mémoire ou thèse est analysé.
            </p>
        </div>
        <!-- End Title -->

        <div>
            <!-- Timeline -->
            <div>
                <div class="mb-4">
                    <h3 class="font-medium text-[#ff0] text-xs uppercase">Étapes clés</h3>
                </div>

                <!-- Étape 1 -->
                <div class="flex gap-x-5 ms-1">
                    <div
                        class="last:after:hidden after:top-8 after:bottom-0 after:absolute relative after:bg-neutral-800 after:w-px after:-translate-x-[0.5px] after:start-4">
                        <div class="z-10 relative flex justify-center items-center size-8">
                            <span
                                class="flex justify-center items-center border border-neutral-800 rounded-full size-8 font-semibold text-[#ff0] text-xs uppercase shrink-0">1</span>
                        </div>
                    </div>
                    <div class="pt-0.5 pb-8 sm:pb-12 grow">
                        <p class="text-neutral-400 text-sm lg:text-base">
                            <span class="text-white">Dépôt du document :</span>
                            Vous nous soumettez votre travail. Nous préparons le contenu pour lancer l’analyse.
                        </p>
                    </div>
                </div>

                <!-- Étape 2 -->
                <div class="flex gap-x-5 ms-1">
                    <div
                        class="last:after:hidden after:top-8 after:bottom-0 after:absolute relative after:bg-neutral-800 after:w-px after:-translate-x-[0.5px] after:start-4">
                        <div class="z-10 relative flex justify-center items-center size-8">
                            <span
                                class="flex justify-center items-center border border-neutral-800 rounded-full size-8 font-semibold text-[#ff0] text-xs uppercase shrink-0">2</span>
                        </div>
                    </div>
                    <div class="pt-0.5 pb-8 sm:pb-12 grow">
                        <p class="text-neutral-400 text-sm lg:text-base">
                            <span class="text-white">Vérification d’originalité :</span>
                            Nous comparons votre texte avec des sources pertinentes pour repérer les passages similaires.
                        </p>
                    </div>
                </div>

                <!-- Étape 3 -->
                <div class="flex gap-x-5 ms-1">
                    <div
                        class="last:after:hidden after:top-8 after:bottom-0 after:absolute relative after:bg-neutral-800 after:w-px after:-translate-x-[0.5px] after:start-4">
                        <div class="z-10 relative flex justify-center items-center size-8">
                            <span
                                class="flex justify-center items-center border border-neutral-800 rounded-full size-8 font-semibold text-[#ff0] text-xs uppercase shrink-0">3</span>
                        </div>
                    </div>
                    <div class="pt-0.5 pb-8 sm:pb-12 grow">
                        <p class="text-neutral-400 text-sm md:text-base">
                            <span class="text-white">Rapport d’analyse :</span>
                            Vous recevez un rapport clair avec :
                        </p>
                        <ul class="mt-3 space-y-2 text-neutral-400 text-sm md:text-base">
                            <li>Le pourcentage global de similitude détecté.</li>
                            <li>Les sources identifiées avec des liens directs.</li>
                            <li>Les passages sensibles surlignés dans votre texte.</li>
                        </ul>
                        <p class="mt-3 text-neutral-400 text-sm md:text-base">
                            Vous pouvez ainsi corriger avant dépôt et présenter un travail fiable.
                        </p>
                    </div>
                </div>

                <!-- CTA -->
                <a class="group inline-flex items-center gap-x-2 bg-[#ff0] px-3 py-2 rounded-full font-medium text-neutral-800 text-sm"
                    href="{{ route('analysis.request') }}" wire:navigate>
                    <span>Demander une analyse</span>
                </a>
            </div>
            <!-- End Timeline -->
        </div>
        <!-- End Grid -->
    </div>
</div>
<!-- End Approach -->
