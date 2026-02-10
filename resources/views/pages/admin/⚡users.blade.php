<?php

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new class extends Component {
    public $users;
    public $roles;

    public function mount()
    {
        $this->users = User::with('roles', 'permissions')->get();
        $this->roles = Role::all();
    }

    public function render()
    {
        return $this->view()->layout('layouts::app');
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
            <span class="font-semibold text-yellow-400 dark:text-yellow-400">Liste des utilisateurs</span>
        </nav>

        <!-- Actions -->
        <div class="flex flex-wrap items-center gap-3">
            @php
$buttonClass =
    'bg-white/10 dark:bg-neutral-800/50 backdrop-blur-md rounded-full p-2 text-gray-600 dark:text-gray-300 hover:text-yellow-500 transition flex items-center gap-x-2';
            @endphp

            <a href="#" id="newUserButton" class="{{ $buttonClass }}">
                <svg class="w-5 h-5 text-current" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M12 2a10 10 0 100 20 10 10 0 000-20Zm1 5.757a1 1 0 10-2 0V11H7.757a1
                        1 0 100 2H11v3.243a1 1 0 102 0V13h3.243a1 1 0 100-2H13V7.757Z" clip-rule="evenodd" />
                </svg>
                Créer un utilisateur
            </a>

            <a href="#" id="newPermissionButton" class="{{ $buttonClass }}">
                <svg class="w-5 h-5 text-current" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M12 2a10 10 0 100 20 10 10 0 000-20Zm1 5.757a1 1 0 10-2 0V11H7.757a1
                        1 0 100 2H11v3.243a1 1 0 102 0V13h3.243a1 1 0 100-2H13V7.757Z" clip-rule="evenodd" />
                </svg>
                Créer une permission
            </a>

            <a href="#" id="newRoleButton" class="{{ $buttonClass }}">
                <svg class="w-5 h-5 text-current" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M12 2a10 10 0 100 20 10 10 0 000-20Zm1 5.757a1 1 0 10-2 0V11H7.757a1
                        1 0 100 2H11v3.243a1 1 0 102 0V13h3.243a1 1 0 100-2H13V7.757Z" clip-rule="evenodd" />
                </svg>
                Créer un rôle
            </a>
        </div>
    </div>
    <!--end breadcrumb-->

    <h6 class="mb-6 px-1 sm:px-3 text-gray-700 dark:text-gray-300 text-sm">
        Consultez la liste de tous les utilisateurs enregistrés
    </h6>


    @if (session('success'))
        <div class="bg-green-500 mb-4 p-4 rounded text-white">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex">
        <div class="dark:bg-neutral-800 p-3 border-e border-gray-200 dark:border-neutral-700">
            <nav class="flex flex-col space-y-2" aria-label="Tabs" role="tablist" aria-orientation="horizontal"
                data-hs-tabs='{"eventType": "click"}'>
                <button type="button"
                    class="inline-flex items-center gap-x-2 disabled:opacity-50 py-1 pe-4 border-e-2 border-transparent hs-tab-active:border-blue-500 focus:outline-hidden text-gray-500 hover:text-blue-600 focus:text-blue-600 hs-tab-active:text-blue-600 dark:hover:text-blue-500 dark:hs-tab-active:text-blue-600 dark:text-neutral-400 text-sm whitespace-nowrap disabled:pointer-events-none active"
                    id="open-on-hover-tab-item-1" aria-selected="true" data-hs-tab="#open-on-hover-tab-1"
                    aria-controls="open-on-hover-tab-1" role="tab">
                    Utilisateurs
                </button>
                <button type="button"
                    class="inline-flex items-center gap-x-2 disabled:opacity-50 py-1 pe-4 border-e-2 border-transparent hs-tab-active:border-blue-500 focus:outline-hidden text-gray-500 hover:text-blue-600 focus:text-blue-600 hs-tab-active:text-blue-600 dark:hover:text-blue-500 dark:hs-tab-active:text-blue-600 dark:text-neutral-400 text-sm whitespace-nowrap disabled:pointer-events-none"
                    id="open-on-hover-tab-item-2" aria-selected="false" data-hs-tab="#open-on-hover-tab-2"
                    aria-controls="open-on-hover-tab-2" role="tab">
                    Attribuer un rôle
                </button>
                <button type="button"
                    class="inline-flex items-center gap-x-2 disabled:opacity-50 py-1 pe-4 border-e-2 border-transparent hs-tab-active:border-blue-500 focus:outline-hidden text-gray-500 hover:text-blue-600 focus:text-blue-600 hs-tab-active:text-blue-600 dark:hover:text-blue-500 dark:hs-tab-active:text-blue-600 dark:text-neutral-400 text-sm whitespace-nowrap disabled:pointer-events-none"
                    id="open-on-hover-tab-item-3" aria-selected="false" data-hs-tab="#open-on-hover-tab-3"
                    aria-controls="open-on-hover-tab-3" role="tab">
                    Rôles et Permissions
                </button>
            </nav>
        </div>

        <div class="ms-6">
            <div id="open-on-hover-tab-1" role="tabpanel" aria-labelledby="open-on-hover-tab-item-1">
                <x-users-table :users="$users" :roles="$roles"></x-users-table>
            </div>
            <div id="open-on-hover-tab-2" class="hidden w-full" role="tabpanel" aria-labelledby="open-on-hover-tab-item-2">
                <x-assign-roles-table></x-assign-roles-table>
            </div>
            <div id="open-on-hover-tab-3" class="hidden w-full" role="tabpanel" aria-labelledby="open-on-hover-tab-item-3">
                <x-roles-permissions-table></x-roles-permissions-table>
            </div>
        </div>
    </div>
</div>
