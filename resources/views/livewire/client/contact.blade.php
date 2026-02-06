<?php

use Livewire\Component;
use App\Models\ContactMessage;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $company = '';
    public string $phone = '';
    public string $message = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'message' => 'required|string|max:2000',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'company' => $this->company ?: null,
            'phone' => $this->phone ?: null,
            'message' => $this->message,
            'submitted_at' => now(),
        ]);

        session()->flash('contact_success', 'Merci ! Votre message a bien été envoyé.');

        $this->reset(['message']);
    }
};
?>

<!-- Contact -->
<div class="bg-neutral-900" id="contact">
    <div class="mx-auto px-4 xl:px-0 py-10 lg:py-20 max-w-5xl">
        <!-- Title -->
        <div class="mb-10 lg:mb-14 max-w-3xl">
            <h2 class="font-semibold text-white text-2xl md:text-4xl md:leading-tight">Nous contacter</h2>
            <p class="mt-1 text-neutral-400">Peu importe votre but, nous vous satisferons.</p>
        </div>
        <!-- End Title -->

        <!-- Grid -->
        <div class="gap-x-10 lg:gap-x-16 grid grid-cols-1 md:grid-cols-2">
            <div class="md:order-2 mb-10 md:mb-0 pb-10 md:pb-0 border-neutral-800 border-b md:border-b-0">
                @if (session()->has('contact_success'))
                    <div class="bg-green-700/80 mb-4 p-3 rounded text-white">
                        {{ session('contact_success') }}
                    </div>
                @endif

                <form wire:submit.prevent="submit">
                    <div class="space-y-4">
                        <!-- Input -->
                        <div class="relative">
                            <input wire:model.defer="name" type="text" id="hs-tac-input-name"
                                class="peer block bg-neutral-800 disabled:opacity-50 p-3 sm:p-4 autofill:pt-6 focus:pt-6 not-placeholder-shown:pt-6 autofill:pb-2 focus:pb-2 not-placeholder-shown:pb-2 border-transparent focus:border-transparent rounded-lg focus:outline-hidden focus:ring-0 w-full text-white placeholder:text-transparent sm:text-sm disabled:pointer-events-none"
                                placeholder="Name" required>
                            <label for="hs-tac-input-name"
                                class="top-0 absolute peer-disabled:opacity-50 p-3 sm:p-4 border border-transparent h-full text-neutral-400 peer-focus:text-neutral-400 peer-not-placeholder-shown:text-neutral-400 peer-focus:text-xs peer-not-placeholder-shown:text-xs text-sm truncate transition peer-focus:-translate-y-1.5 peer-not-placeholder-shown:-translate-y-1.5 duration-100 ease-in-out pointer-events-none peer-disabled:pointer-events-none start-0">Votre
                                nom</label>
                            @error('name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <!-- End Input -->

                        <!-- Input -->
                        <div class="relative">
                            <input wire:model.defer="email" type="email" id="hs-tac-input-email"
                                class="peer block bg-neutral-800 disabled:opacity-50 p-3 sm:p-4 autofill:pt-6 focus:pt-6 not-placeholder-shown:pt-6 autofill:pb-2 focus:pb-2 not-placeholder-shown:pb-2 border-transparent focus:border-transparent rounded-lg focus:outline-hidden focus:ring-0 w-full text-white placeholder:text-transparent sm:text-sm disabled:pointer-events-none"
                                placeholder="Email" required>
                            <label for="hs-tac-input-email"
                                class="top-0 absolute peer-disabled:opacity-50 p-3 sm:p-4 border border-transparent h-full text-neutral-400 peer-focus:text-neutral-400 peer-not-placeholder-shown:text-neutral-400 peer-focus:text-xs peer-not-placeholder-shown:text-xs text-sm truncate transition peer-focus:-translate-y-1.5 peer-not-placeholder-shown:-translate-y-1.5 duration-100 ease-in-out pointer-events-none peer-disabled:pointer-events-none start-0">Votre
                                adresse mail</label>
                            @error('email') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <!-- End Input -->

                        <!-- Input -->
                        <div class="relative">
                            <input wire:model.defer="company" type="text" id="hs-tac-input-company"
                                class="peer block bg-neutral-800 disabled:opacity-50 p-3 sm:p-4 autofill:pt-6 focus:pt-6 not-placeholder-shown:pt-6 autofill:pb-2 focus:pb-2 not-placeholder-shown:pb-2 border-transparent focus:border-transparent rounded-lg focus:outline-hidden focus:ring-0 w-full text-white placeholder:text-transparent sm:text-sm disabled:pointer-events-none"
                                placeholder="Company">
                            <label for="hs-tac-input-company"
                                class="top-0 absolute peer-disabled:opacity-50 p-3 sm:p-4 border border-transparent h-full text-neutral-400 peer-focus:text-neutral-400 peer-not-placeholder-shown:text-neutral-400 peer-focus:text-xs peer-not-placeholder-shown:text-xs text-sm truncate transition peer-focus:-translate-y-1.5 peer-not-placeholder-shown:-translate-y-1.5 duration-100 ease-in-out pointer-events-none peer-disabled:pointer-events-none start-0">Entreprise</label>
                            @error('company') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <!-- End Input -->

                        <!-- Input -->
                        <div class="relative">
                            <input wire:model.defer="phone" type="text" id="hs-tac-input-phone"
                                class="peer block bg-neutral-800 disabled:opacity-50 p-3 sm:p-4 autofill:pt-6 focus:pt-6 not-placeholder-shown:pt-6 autofill:pb-2 focus:pb-2 not-placeholder-shown:pb-2 border-transparent focus:border-transparent rounded-lg focus:outline-hidden focus:ring-0 w-full text-white placeholder:text-transparent sm:text-sm disabled:pointer-events-none"
                                placeholder="Phone">
                            <label for="hs-tac-input-phone"
                                class="top-0 absolute peer-disabled:opacity-50 p-3 sm:p-4 border border-transparent h-full text-neutral-400 peer-focus:text-neutral-400 peer-not-placeholder-shown:text-neutral-400 peer-focus:text-xs peer-not-placeholder-shown:text-xs text-sm truncate transition peer-focus:-translate-y-1.5 peer-not-placeholder-shown:-translate-y-1.5 duration-100 ease-in-out pointer-events-none peer-disabled:pointer-events-none start-0">Numéro
                                de téléphone</label>
                            @error('phone') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <!-- End Input -->

                        <!-- Textarea -->
                        <div class="relative">
                            <textarea wire:model.defer="message" id="hs-tac-message"
                                class="peer block bg-neutral-800 disabled:opacity-50 p-3 sm:p-4 autofill:pt-6 focus:pt-6 not-placeholder-shown:pt-6 autofill:pb-2 focus:pb-2 not-placeholder-shown:pb-2 border-transparent focus:border-transparent rounded-lg focus:outline-hidden focus:ring-0 w-full text-white placeholder:text-transparent sm:text-sm disabled:pointer-events-none"
                                placeholder="This is a textarea placeholder" data-hs-textarea-auto-height required></textarea>
                            <label for="hs-tac-message"
                                class="top-0 absolute peer-disabled:opacity-50 p-3 sm:p-4 border border-transparent h-full text-neutral-400 peer-focus:text-neutral-400 peer-not-placeholder-shown:text-neutral-400 peer-focus:text-xs peer-not-placeholder-shown:text-xs text-sm truncate transition peer-focus:-translate-y-1.5 peer-not-placeholder-shown:-translate-y-1.5 duration-100 ease-in-out pointer-events-none peer-disabled:pointer-events-none start-0">Dites-nous
                                en quoi nous pouvons vous aider...</label>
                            @error('message') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <!-- End Textarea -->
                    </div>

                    <div class="mt-2">
                        <p class="text-neutral-500 text-xs">
                            *Le message et vos coordonnées sont requis
                        </p>

                        <p class="mt-5">
                            <button type="submit"
                                class="group inline-flex items-center gap-x-2 bg-[#ff0] px-3 py-2 rounded-full focus:outline-hidden font-medium text-neutral-800 text-sm">
                                Envoyer
                                <svg class="size-4 transition group-focus:translate-x-0 group-hover:translate-x-0.5 shrink-0"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14" />
                                    <path d="m12 5 7 7-7 7" />
                                </svg>
                            </button>
                        </p>
                    </div>
                </form>
            </div>
            <!-- End Col -->

            <div class="space-y-14">
                <!-- Item -->
                <div class="flex gap-x-5">
                    <svg class="size-6 text-neutral-500 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    <div class="grow">
                        <h4 class="font-semibold text-white">Où nous trouver:</h4>

                        <address class="mt-1 text-neutral-400 text-sm not-italic">
                            1 Zongotolo, Kinshasa - Gombe, RDC. <br>
                            République Démocratique du Congo
                        </address>
                    </div>
                </div>
                <!-- End Item -->

                <!-- Item -->
                <div class="flex gap-x-5">
                    <svg class="size-6 text-neutral-500 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M21.2 8.4c.5.38.8.97.8 1.6v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V10a2 2 0 0 1 .8-1.6l8-6a2 2 0 0 1 2.4 0l8 6Z" />
                        <path d="m22 10-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 10" />
                    </svg>
                    <div class="grow">
                        <h4 class="font-semibold text-white">Email:</h4>

                        <a class="mt-1 focus:outline-hidden text-neutral-400 hover:text-neutral-200 focus:text-neutral-200 text-sm"
                            href="mailto:contact@vinify.com" target="_blank">
                            contact@vinify.com
                        </a>
                    </div>
                </div>
                <!-- End Item -->

                <!-- Item -->
                <div class="flex gap-x-5">
                    <svg class="size-6 text-neutral-500 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 11 18-5v12L3 14v-3z" />
                        <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6" />
                    </svg>
                    <div class="grow">
                        <h4 class="font-semibold text-white">Nous engageons!</h4>
                        <p class="mt-1 text-neutral-400">Nous sommes heureux d'annoncer que nous agrandissons notre
                            équipe et
                            nous recherchons des personnes talentueuses pour se joindre à notre aventure.</p>
                        <p class="mt-2">
                            <a class="group inline-flex items-center gap-x-2 focus:outline-hidden font-medium text-[#ff0] text-sm decoration-2 hover:underline focus:underline"
                                href="#">
                                Intéressé.e?
                                <svg class="size-4 transition group-focus:translate-x-0 group-hover:translate-x-0.5 shrink-0"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14" />
                                    <path d="m12 5 7 7-7 7" />
                                </svg>
                            </a>
                        </p>
                    </div>
                </div>
                <!-- End Item -->
            </div>
            <!-- End Col -->
        </div>
        <!-- End Grid -->
    </div>
</div>
