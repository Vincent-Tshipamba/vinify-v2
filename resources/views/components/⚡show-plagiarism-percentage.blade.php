<?php

use Livewire\Component;

new class extends Component {
    public $percentage = 0 ;
    public $aiGeneratedProbability = 0;

    public function mount($percentage)
    {
        $this->percentage = $percentage;
    }
};
?>

<div>
    <div class="flex flex-col items-center space-y-4 p-6">
        <div
            class="flex md:flex-row flex-col items-center bg-white hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 shadow-sm mx-auto border-gray-200 rounded-lg w-full">
            <img class="md:rounded-none md:rounded-s-lg rounded-t-lg w-full md:w-48 h-full object-cover"
                src="{{ asset('vinify.png') }}" alt="">
            {{-- <div class="flex flex-col justify-between p-4 leading-normal">
                <h5 class="font-bold text-gray-900 dark:text-white text-xl text-center tracking-tight">
                    Pourcentage de plagiat détecté : {{ $percentage }}%
                </h5>
                <p
                    class="mb-3 text-2xl text-start font-extrabold text-gray-700 {{ $percentage > 0 ? 'dark:text-red-500' : 'dark:text-green-500' }}">
                    {{ $percentage > 0 ? '(Plagiat détecté)' : '(Aucun plagiat détecté)' }}
                </p>
            </div> --}}
            <div class="flex flex-col justify-between p-4 leading-normal">
                <h5 class="mb-2 font-bold text-gray-900 dark:text-white text-2xl tracking-tight">
                    Pourcentage de plagiat : <span
                        class="{{ $percentage > 0 ? ($percentage < 15 ? 'dark:text-orange-500' : 'dark:text-red-500') : 'dark:text-green-500' }}">{{ $percentage }}%</span>
                </h5>
                <p
                    class="mb-3 font-normal {{ $percentage > 0 ? ($percentage < 15 ? 'dark:text-orange-500' : 'dark:text-red-500') : 'dark:text-green-500' }}">
                    @if ($percentage > 0)
                        @if ($percentage <= 15)
                            (Plagiat modéré détecté)
                        @elseif($percentage > 15)
                            (Plagiat critique détecté)
                        @else
                            (Plagiat faible détecté)
                        @endif
                    @else
                        (Aucun plagiat détecté)
                    @endif
                </p>
            </div>
        </div>
        {{-- <div
            class="flex md:flex-row flex-col items-center bg-white hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 shadow-sm mx-auto border-gray-200 rounded-lg w-full">
            <img class="md:rounded-none md:rounded-s-lg rounded-t-lg w-full md:w-48 h-full object-cover"
                src="{{ asset('img/vinify.png') }}" alt="">
            <div class="flex flex-col justify-between p-4 leading-normal">
                <h5 class="font-bold text-gray-900 dark:text-white text-xl text-center tracking-tight">
                    Pourcentage de plagiat détecté : {{ $percentage }}%
                </h5>
                <p
                    class="mb-3 text-2xl text-start font-extrabold text-gray-700 {{ $percentage > 0 ? 'dark:text-red-500' : 'dark:text-green-500' }}">
                    {{ $percentage > 0 ? '(Plagiat détecté)' : '(Aucun plagiat détecté)' }}
                </p>
            </div>
            <div class="flex flex-col justify-between p-4 leading-normal">
                <h5 class="mb-2 font-bold text-gray-900 dark:text-white text-2xl tracking-tight">
                    Pourcentage de plagiat via IA : <span
                        class="{{ $aiGeneratedProbability > 0 ? ($aiGeneratedProbability < 15 ? 'dark:text-orange-500' : 'dark:text-red-500') : 'dark:text-green-500' }}">{{ $aiGeneratedProbability }}%</span>
                </h5>
                <p
                    class="mb-3 font-normal {{ $aiGeneratedProbability > 0 ? ($aiGeneratedProbability < 50 ? 'dark:text-orange-500' : 'dark:text-red-500') : 'dark:text-green-500' }}">
                    @if ($aiGeneratedProbability > 0)
                        @if ($aiGeneratedProbability <= 50)
                            (Plagiat modéré détecté)
                        @elseif($aiGeneratedProbability > 50)
                            (Plagiat critique détecté)
                        @else
                            (Plagiat faible détecté)
                        @endif
                    @else
                        (Aucun plagiat détecté)
                    @endif
                </p>
            </div>
        </div> --}}

    </div>
</div>
