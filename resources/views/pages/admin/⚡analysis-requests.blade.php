<?php

use Livewire\Component;
use App\Models\AnalysisRequest;

new class extends Component {
    public $requests;

    public function mount()
    {
        $this->requests = AnalysisRequest::with('user', 'university', 'document')->get();
    }
};
?>

<div>
    <div class="flex sm:flex-row flex-col sm:justify-between sm:items-center gap-y-4 mb-6 page-breadcrumb">
        <!-- Breadcrumb -->
        <nav
            class="flex items-center gap-x-2 bg-white/10 dark:bg-neutral-800/50 shadow-sm backdrop-blur-md px-6 py-3 rounded-xl text-gray-500 dark:text-gray-400 text-sm">
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-x-1 font-medium hover:text-gray-700 dark:hover:text-white">
                <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-7-7v18" />
                </svg>
                <span>Dashboard</span>
            </a>
            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4
                            4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-semibold text-yellow-400 dark:text-yellow-400">Liste des requêtes d'analyse</span>
        </nav>
    </div>

    <h6 class="mb-6 px-1 sm:px-3 text-gray-700 dark:text-gray-300 text-sm">
        Consultez la liste de toutes les demandes d'analyse reçues
    </h6>


    @if (session('success'))
        <div class="bg-green-500 mb-4 p-4 rounded text-white">
            {{ session('success') }}
        </div>
    @endif

    <div class="w-full overflow-x-auto">
        <table border="1" id="requestsTable" class="table-bordered table-striped row-border w-full display">
            <thead>
                <tr>
                    <th class="dark:bg-neutral-800">
                        #
                    </th>
                    <th class="dark:bg-neutral-800">
                        Nom d'utilisateur
                    </th>
                    <th class="dark:bg-neutral-800">
                        Université
                    </th>
                    <th class="dark:bg-neutral-800">
                        Titre du document
                    </th>
                    <th class="dark:bg-neutral-800">
                        Date de soumission
                    </th>
                    <th class="dark:bg-neutral-800">
                        Statut
                    </th>
                    <th class="dark:bg-neutral-800">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requests as $key => $request)
                    <tr
                        class="hover:bg-[#f0e6d9] dark:hover:bg-gray-800 hover:scale-100 transition-all duration-300 ease-in-out hover:cursor-pointer">
                        <td>{{ $key + 1 }}</td>

                        <td class="size-px whitespace-nowrap"
                            onclick="showUserProfile({{ $request->user->id }}, '{{ $request->user->name }}', '{{ $request->user->email }}', '{{ $request->user->created_at }}', '{{ $request->user->last_activity }}', '{{ $cache_exists ?? '' }}')">

                            <div class="py-3 ps-6 lg:ps-3 xl:ps-0 pe-6">
                                <div class="flex items-center gap-x-3">
                                    <img class="inline-block rounded-full size-9.5"
                                        src="{{ $request->user->profile_picture ? asset($request->user->profile_picture) : 'https://images.unsplash.com/photo-1531927557220-a9e23c1e4794?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=320&h=320&q=80' }}"
                                        alt="Avatar">
                                    <div class="grow">
                                        <span class="block font-semibold text-gray-800 dark:text-neutral-200 text-sm">
                                            {{ $request->user->name }}
                                        </span>
                                        <span class="block text-gray-500 dark:text-neutral-500 text-sm">
                                            {{ $request->user->email }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="w-72 h-px whitespace-nowrap">
                            <div class="">
                                <span class="block font-semibold text-gray-800 dark:text-neutral-200 text-sm">
                                    {{ $request->university?->name ?? 'N/D' }}
                                </span>
                            </div>
                        </td>

                        <td class="">
                            <div class="">
                                <span class="block font-semibold text-gray-800 dark:text-neutral-200 text-sm">
                                    {{ $request->document?->name ?? 'N/D' }}
                                </span>
                            </div>
                        </td>
                        <td class="size-px whitespace-nowrap">
                            <div class="">
                                <span class="block font-semibold text-gray-800 dark:text-neutral-200 text-sm">
                                    {{ $request->submitted_at->format('d-m-Y') }}
                                </span>
                            </div>
                        </td>
                        <td class="size-px whitespace-nowrap">
                            <div class="">
                                <span class="block font-semibold text-gray-800 dark:text-neutral-200 text-sm">
                                    @if ($request->status == 'in_progress')
                                        En cours de traitement
                                    @elseif ($request->status == 'cancelled')
                                        Annulée
                                    @elseif ($request->status == 'processed')
                                        Déjà traitée
                                    @else
                                        En attente
                                    @endif
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="flex justify-center items-center space-x-1">
                                <a href="{{ route('requests.show', $request->id) }}" wire:navigate>
                                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                        <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span>
                                        Voir
                                    </span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
