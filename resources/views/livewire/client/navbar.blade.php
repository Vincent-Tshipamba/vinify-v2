<?php

use Livewire\Component;

new class extends Component {
};
?>

<!-- ========== HEADER ========== -->
<header
    class="top-4 z-50 before:absolute sticky inset-x-0 before:inset-0 flex flex-wrap md:flex-nowrap md:justify-start before:bg-neutral-800/30 before:backdrop-blur-md lg:before:mx-auto before:mx-2 before:rounded-full w-full before:max-w-5xl">
    <nav
        class="relative md:flex md:justify-between md:items-center mx-2 lg:mx-auto py-2.5 md:py-0 ps-5 pe-2 w-full max-w-5xl">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <!-- Logo -->
                <a class="inline-block flex-none focus:opacity-80 rounded-md focus:outline-hidden font-semibold text-[#ff0] text-xl"
                    href="{{ route('home') }}" aria-label="Preline" wire:navigate>
                    Vinify
                </a>
                <!-- End Logo -->

                <div class="ms-1 sm:ms-2 w-12">
                    <a href="{{ route('home') }}" wire:navigate>
                        <img src="{{ asset('vinify.png') }}" alt="" srcset="">
                    </a>
                </div>
            </div>

            <div class="md:hidden">
                <button type="button"
                    class="hs-collapse-toggle flex justify-center items-center bg-neutral-800 disabled:opacity-50 rounded-full size-8 font-semibold text-white text-sm disabled:pointer-events-none"
                    id="hs-navbar-floating-dark-collapse" aria-expanded="false" aria-controls="hs-navbar-floating-dark"
                    aria-label="Toggle navigation" data-hs-collapse="#hs-navbar-floating-dark">
                    <svg class="hs-collapse-open:hidden size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" x2="21" y1="6" y2="6" />
                        <line x1="3" x2="21" y1="12" y2="12" />
                        <line x1="3" x2="21" y1="18" y2="18" />
                    </svg>
                    <svg class="hidden hs-collapse-open:block size-4 shrink-0" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Collapse -->
        <div id="hs-navbar-floating-dark"
            class="hidden hs-collapse md:block overflow-hidden transition-all duration-300 basis-full grow"
            aria-labelledby="hs-navbar-floating-dark-collapse">
            <div class="flex md:flex-row flex-col md:justify-end md:items-center gap-y-3 py-2 md:py-0 md:ps-7">
                <a class="sm:px-3 md:py-4 ps-px pe-3 focus:outline-hidden text-white hover:text-neutral-300 focus:text-neutral-300 text-sm"
                    href="{{ route('home') }}" aria-current="page" wire:navigate>Accueil</a>

                <a class="sm:px-3 md:py-4 ps-px pe-3 focus:outline-hidden text-white hover:text-neutral-300 focus:text-neutral-300 text-sm"
                    href="{{ route('home') }}#stats">Stats</a>

                <a class="sm:px-3 md:py-4 ps-px pe-3 focus:outline-hidden text-white hover:text-neutral-300 focus:text-neutral-300 text-sm"
                    href="{{ route('home') }}#approach">Approche</a>

                <a class="sm:px-3 md:py-4 ps-px pe-3 focus:outline-hidden text-white hover:text-neutral-300 focus:text-neutral-300 text-sm"
                    href="{{ route('home') }}#about">A propos</a>

                <div>
                    <a class="group inline-flex items-center gap-x-2 bg-[#ff0] px-3 py-2 rounded-full focus:outline-hidden font-medium text-neutral-800 text-sm"
                        href="{{ route('analysis.request') }}" wire:navigate>
                        <span>Demander une analyse</span>
                    </a>
                </div>
            </div>
        </div>
        <!-- End Collapse -->
    </nav>
</header>
<!-- ========== END HEADER ========== -->
