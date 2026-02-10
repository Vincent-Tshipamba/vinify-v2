@props(['users', 'roles'])
<div class="w-full">
    <div class="w-full">
        <div class="w-full overflow-x-auto">
            <table border="1" id="users-table" class="w-full table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="dark:bg-neutral-800">
                            <span class="flex items-center">
                                #
                            </span>
                        </th>
                        <th class="dark:bg-neutral-800">
                            <span class="flex items-center">
                                Nom d'utilisateur
                            </span>
                        </th>
                        <th class="dark:bg-neutral-800">
                            <span class="flex items-center">
                                Université
                            </span>
                        </th>
                        <th class="dark:bg-neutral-800">
                            <span class="flex items-center">
                                Role
                            </span>
                        </th>
                        <th class="dark:bg-neutral-800">
                            <span class="flex items-center">
                                Statut
                            </span>
                        </th>
                        <th class="dark:bg-neutral-800">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $key => $user)
                        @php
                            $cache_exists = false;
                            if (Cache::has('user-is-online-' . $user->id)) {
                                $cache_exists = true;
                            }
                        @endphp
                        <tr
                            class="hover:bg-[#f0e6d9] dark:hover:bg-gray-800 hover:scale-100 transition-all duration-300 ease-in-out">
                            <td>{{ $key + 1 }}</td>

                            <td class="size-px whitespace-nowrap"
                                onclick="showUserProfile({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->created_at }}', '{{ $user->last_activity }}', '{{ $cache_exists }}')">

                                <div class="py-3 ps-6 lg:ps-3 xl:ps-0 pe-6">
                                    <div class="flex items-center gap-x-3">
                                        <img class="inline-block rounded-full size-9.5"
                                            src="{{ $user->client?->image ? asset($user->client->image) : 'https://images.unsplash.com/photo-1531927557220-a9e23c1e4794?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=320&h=320&q=80' }}"
                                            alt="Avatar">
                                        <div class="grow">
                                            <span
                                                class="block font-semibold text-gray-800 dark:text-neutral-200 text-sm">
                                                {{ $user->name }}
                                            </span>
                                            <span class="block text-gray-500 dark:text-neutral-500 text-sm">
                                                {{ $user->email }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="w-72 h-px whitespace-nowrap">
                                <div class="px-6 py-3">
                                    <span
                                        class="block font-semibold text-gray-800 dark:text-neutral-200 text-sm">
                                        {{ $user->university?->name ?? 'N/D' }}
                                    </span>
                                </div>
                            </td>

                            <td class="">
                                @foreach ($roles as $role)
                                    {{ $user->roles->contains($role) ? $role->name : '' }}
                                @endforeach
                            </td>
                            <td class="size-px whitespace-nowrap">
                                <div class="px-6 py-3">
                                    @if ($cache_exists)
                                        <span
                                            class="inline-flex items-center gap-x-1 bg-teal-100 dark:bg-teal-500/10 px-1.5 py-1 rounded-full font-medium text-teal-800 dark:text-teal-500 text-xs">
                                            <svg class="size-2.5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                viewBox="0 0 16 16">
                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417
                                                    5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75
                                                    0 0 0-.01-1.05z" />
                                            </svg>
                                            En ligne
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-x-1 bg-red-100 dark:bg-red-500/10 px-1.5 py-1 rounded-full font-medium text-red-800 dark:text-red-500 text-xs">
                                            <svg class="size-2.5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                viewBox="0 0 16 16">
                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM4.646 4.646a.5.5 0 0 1 .708 0L8
                                                    7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5
                                                    0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293
                                                    8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                                            </svg>
                                            Inactif
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" value="" class="sr-only peer"
                                        onchange="changeUserStatus({{ $user->id }})"
                                        {{ $user->is_active ? 'checked' : '' }}>
                                    <div
                                        class="peer after:top-0.5 after:absolute relative bg-gray-200 after:bg-white dark:bg-gray-700 peer-checked:bg-[#e38407] after:border after:border-gray-300 dark:border-gray-600 peer-checked:after:border-white rounded-full after:rounded-full dark:peer-focus:ring-blue-800 peer-focus:ring-4 peer-focus:ring-blue-300 w-11 after:w-5 h-6 after:h-5 after:content-[''] after:transition-all rtl:peer-checked:after:-translate-x-full peer-checked:after:translate-x-full after:start-0.5">
                                    </div>
                                </label>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>