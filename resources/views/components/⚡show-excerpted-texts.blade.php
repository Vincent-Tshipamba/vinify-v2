<?php

use Livewire\Component;

new class extends Component {
    public $excerptedText;

    public function mount($excerptedText)
    {
        $this->excerptedText = $excerptedText;
    }

};
?>

<div>
    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if ($excerptedText)
        @foreach ($excerptedText as $excerpt)
            <p class="bg-gray-800 mb-8 p-2 rounded overflow-auto text-white whitespace-pre-wrap">
                {{ $excerpt['plagiarized_text'] }}
            </p>
        @endforeach
    @else
        <p>Aucun extrait de texte disponible.</p>
    @endif
</div>
