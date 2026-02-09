<?php

use App\Models\User;
use App\Models\Corpus;
use Livewire\Component;
use App\Models\Document;
use App\Models\University;
use Illuminate\Support\Str;
use App\Models\TextAnalysis;
use Livewire\WithFileUploads;
use App\Models\AnalysisRequest;
use Illuminate\Support\Facades\Hash;
use App\Jobs\SendAnalysisRequestEmail;

new class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $university_name = '';
    public string $subject = '';
    public $document;
    public string $message = '';

    public array $universitySuggestions = [];

    public function mount(): void
    {
        $this->universitySuggestions = University::orderBy('name')->pluck('name')->all();
    }

    public function render()
    {
        return $this->view()->layout('layouts::client');
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => [
                'required',
                'string',
                'size:9',
                'regex:/^([89][0-9]{8})$/',
            ],
            'university_name' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,docx|max:10240',
            'message' => 'nullable|string|max:2000',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $user = User::firstOrCreate(
            ['email' => $this->email],
            [
                'name' => $this->name,
                'password' => Hash::make(Str::random(16)),
                'avatar' => '',
            ]
        );

        $university = null;
        if (!empty(trim($this->university_name))) {
            $normalized = Str::slug($this->university_name);
            $university = University::where('slug', $normalized)
                ->orWhere('name', 'like', '%' . $this->university_name . '%')
                ->first();

            if (!$university) {
                $university = University::create([
                    'name' => $this->university_name,
                    'slug' => $normalized,
                    'admin_id' => $user->id,
                ]);
            }

            if ($user->university_id !== $university->id) {
                $user->university_id = $university->id;
                $user->save();
            }
        }

        $originalName = $this->document->getClientOriginalName();
        $extension = strtolower($this->document->getClientOriginalExtension() ?: $this->document->extension());
        $fileHash = hash_file('sha256', $this->document->getRealPath());
        $filePath = $this->document->store('documents');

        $document = Document::create([
            'name' => $this->subject,
            'file_url' => $filePath,
            'file_original_extension' => $extension,
            'file_hash' => $fileHash,
            'user_id' => $user->id,
        ]);

        $globalCorpus = Corpus::firstOrCreate([
            'type' => 'GLOBAL',
            'university_id' => null,
        ]);
        $document->corpus()->syncWithoutDetaching([$globalCorpus->id]);

        $universityCorpus = null;
        if ($university) {
            $universityCorpus = Corpus::firstOrCreate([
                'type' => 'UNIVERSITY',
                'university_id' => $university->id,
            ]);
            $document->corpus()->syncWithoutDetaching([$universityCorpus->id]);
        }

        $analysisRequest = AnalysisRequest::create([
            'user_id' => $user->id,
            'university_id' => $university?->id,
            'document_id' => $document->id,
            'status' => 'submitted',
            'submitted_at' => now(),
            'additional_infos' => [
                'phone' => $this->phone,
                'message' => $this->message,
                'original_filename' => $originalName,
            ],
        ]);

        TextAnalysis::create([
            'user_id' => $user->id,
            'analysis_request_id' => $analysisRequest->id,
            'document_id' => $document->id,
            'corpus_id' => ($universityCorpus ?? $globalCorpus)->id,
            'status' => 'pending',
        ]);

        try {
            // Dispatch email sending job asynchronously
            SendAnalysisRequestEmail::dispatch(
                name: $this->name,
                email: $this->email,
                phone: $this->phone,
                university: $this->university_name,
                file_subject: $this->subject,
                originalFilename: $originalName
            );

        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }

        session()->flash('success', 'Votre demande a bien été enregistrée. Nous vous contacterons bientôt.');

        $this->reset(['subject', 'document', 'message', 'phone']);
    }
};
?>

<div class="bg-neutral-900 py-16">
    <div class="mx-auto px-4 max-w-5xl">
        <div class="mb-10">
            <h1 class="font-semibold text-white text-2xl md:text-3xl">Demande d’analyse</h1>
            <p class="mt-2 text-neutral-400">Renseignez les informations essentielles. Nous vous confirmons la prise en
                charge par email.</p>
        </div>

        <div class="gap-10 grid grid-cols-1 lg:grid-cols-3">
            <div class="lg:col-span-2">
                @if (session()->has('success'))
                    <div class="bg-green-700/80 mb-5 p-3 rounded text-white">
                        {{ session('success') }}
                    </div>
                @endif

                <form wire:submit.prevent="submit" class="space-y-6">
                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div>
                            <label for="name" class="text-neutral-300 text-sm">Nom complet</label>
                            <input id="name" wire:model.defer="name" type="text" required
                                class="bg-neutral-800 mt-1 p-3 rounded-lg w-full text-white"
                                placeholder="Ex: Rebecca Mukendi" />
                            @error('name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="email" class="text-neutral-300 text-sm">Email</label>
                            <input id="email" inputmode="email" wire:model.defer="email" type="email" required
                                class="bg-neutral-800 mt-1 p-3 rounded-lg w-full text-white"
                                placeholder="exemple@email.com" />
                            @error('email') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div>
                            <label for="phone" class="text-neutral-300 text-sm">Numéro de téléphone</label>
                            <input id="phone" inputmode="numeric" wire:model.defer="phone" type="text" required maxlength="9"
                                class="bg-neutral-800 mt-1 p-3 rounded-lg w-full text-white"
                                pattern="^([89][0-9]{8})$" placeholder="99 000 0000" />
                            @error('phone') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="university_name" class="text-neutral-300 text-sm">Université (optionnel)</label>
                            <input id="university_name" wire:model.defer="university_name" type="text"
                                list="universities" class="bg-neutral-800 mt-1 p-3 rounded-lg w-full text-white"
                                placeholder="Commencez à taper..." />
                            <datalist id="universities">
                                @foreach ($universitySuggestions as $suggestion)
                                    <option value="{{ $suggestion }}"></option>
                                @endforeach
                            </datalist>
                            @error('university_name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div>
                            <label for="subject" class="text-neutral-300 text-sm">Sujet du travail</label>
                            <input id="subject" wire:model.defer="subject" type="text" required
                                class="bg-neutral-800 mt-1 p-3 rounded-lg w-full text-white"
                                placeholder="Sujet de votre TFC / TFE / Mémoire / Thèse" />
                            @error('subject') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div x-data="{ isUploading: false, progress: 0 }"
                            x-on:livewire-upload-start="isUploading = true"
                            x-on:livewire-upload-finish="isUploading = false; progress = 0"
                            x-on:livewire-upload-error="isUploading = false"
                            x-on:livewire-upload-progress="progress = $event.detail.progress">
                            <label for="document-input" class="text-neutral-300 text-sm">Document à analyser</label>
                            <input id="document-input" wire:model="document" type="file" accept=".pdf,.docx" required
                                class="bg-neutral-800 mt-1 p-3 rounded-lg w-full text-neutral-300 text-sm" />
                            <p class="mt-1 text-neutral-500 text-xs">Formats acceptés : PDF, DOCX (max 10 Mo).</p>
                            @error('document') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror

                            <div class="mt-2">
                                <button id="preview-document" type="button"
                                    class="bg-neutral-800 hover:bg-neutral-700 px-3 py-2 rounded-md text-neutral-200 text-xs">
                                    Prévisualiser
                                </button>
                            </div>

                            <div class="mt-3" x-show="isUploading">
                                <div class="bg-neutral-800 rounded h-2">
                                    <div class="bg-[#ff0] rounded h-2" :style="{ width: progress + '%' }"></div>
                                </div>
                                <p class="mt-1 text-neutral-500 text-xs" x-text="progress + '%'"></p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="message" class="text-neutral-300 text-sm">Message (optionnel)</label>
                        <textarea id="message" wire:model.defer="message" rows="5"
                            class="bg-neutral-800 mt-1 p-3 rounded-lg w-full text-white"
                            placeholder="Précisez l’objectif, le délai souhaité, ou toute info utile..."></textarea>
                    </div>

                    <div class="flex flex-col gap-3">
                        <p class="text-neutral-500 text-xs">
                            * Les champs obligatoires nous aident à traiter votre demande rapidement.
                        </p>
                        <button type="submit" wire:loading.attr="disabled"
                            class="bg-[#ff0] px-5 py-2.5 rounded-full font-medium text-neutral-800 text-sm">
                            <span wire:loading.remove>Envoyer la demande</span>
                            <span wire:loading>Envoi en cours...</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="space-y-4">
                <div class="bg-neutral-800/60 p-5 rounded-xl">
                    <p class="font-semibold text-white">Ce que vous recevez</p>
                    <ul class="space-y-2 mt-3 text-neutral-400 text-sm">
                        <li>Un taux de similitude clair.</li>
                        <li>Les sources identifiées avec liens.</li>
                        <li>Des passages sensibles à corriger.</li>
                    </ul>
                </div>

                <div class="bg-neutral-800/60 p-5 rounded-xl">
                    <p class="font-semibold text-white">Conseil rapide</p>
                    <p class="mt-2 text-neutral-400 text-sm">
                        Plus le sujet est précis, plus l’analyse est pertinente.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')

@endpush