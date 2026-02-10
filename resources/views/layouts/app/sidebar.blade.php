<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="bg-white dark:bg-zinc-800 min-h-screen">
    <flux:sidebar sticky collapsible="mobile"
        class="bg-zinc-50 dark:bg-zinc-900 border-e border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Platform')" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
                <flux:sidebar.item wire:navigate>
                    Requests
                </flux:sidebar.item>
                <flux:sidebar.item icon="users" :href="route('users.index')"
                    :current="request()->routeIs('users.index')" wire:navigate>
                    Users
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 font-normal text-sm">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-sm text-start">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="flex-1 grid text-sm text-start leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.min.js" defer></script>

    <script src="{{ asset('js/script-user-management.js') }}" defer></script>
    <script>
        function getUsersRoles() {
            $.ajax({
                url: '/users-roles',
                method: "GET",
                success: function (response) {
                    var users = response.users;
                    var roles = response.roles;
                    var userRoles = response.userRoles;

                    // Create the table header with roles
                    var header = '<tr class="bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 border-b-2"><th class="px-6 py-4 font-semibold text-left">Utilisateur</th>';
                    roles.forEach(function (role) {
                        header +=
                            '<th class="px-6 py-4 font-semibold text-center">' + role.name + '</th>';
                    });
                    header += '</tr>';
                    $('#usersRolesTable thead').html(header);

                    // Create the table body with users and checkboxes
                    var body = '';

                    // Initial rendering of the table
                    users.forEach(function (user) {
                        body +=
                            '<tr class="hover:bg-gray-100 dark:hover:bg-gray-700 border-gray-200 dark:border-gray-700 border-b"><th class="px-6 py-3 font-medium text-left"><a href="#" class="hover:text-yellow-500 dark:hover:text-yellow-400" data-user-id="' +
                            user.id + '" data-user-name="' + user.name + '">' + user.name + '</a></th>';
                        roles.forEach(function (role) {
                            var checked = userRoles[user.id] && userRoles[user.id].includes(role
                                .id) ? 'checked' : '';
                            body +=
                                '<td class="px-6 py-3 text-center"><input type="checkbox" class="w-5 h-5 accent-indigo-500 user-checkbox" data-role-id="' +
                                role.id + '" data-user-id="' + user.id +
                                '" ' + checked + '></td>';
                        });
                        body += '</tr>';
                    });

                    $('#usersRolesTable tbody').html(body);

                    // Attach change event listeners to checkboxes
                    var requestInProgress = false;

                    $('#usersRolesTable').on('change', 'input.user-checkbox', function () {
                        if (requestInProgress) {
                            return;
                        }

                        requestInProgress = true;

                        var roleId = $(this).data('role-id');
                        var userId = $(this).data('user-id');
                        var checked = $(this).is(':checked');
                        // Your existing AJAX logic to update user's roles on the server
                        $.ajax({
                            url: '/users/roles/update',
                            method: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                role_id: roleId,
                                user_id: userId,
                                assign: checked
                            },
                            success: function (response) {
                                Swal.fire({
                                    title: 'Succès!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    timerProgressBar: true,
                                    theme: 'dark',
                                    color: '#ffffff',
                                    background: '#000000',
                                    showClass: {
                                        popup: 'swal2-show',
                                        backdrop: 'swal2-backdrop-show',
                                        icon: 'swal2-icon-show'
                                    },
                                    hideClass: {
                                        popup: 'swal2-hide',
                                        backdrop: 'swal2-backdrop-hide',
                                        icon: 'swal2-icon-hide'
                                    },
                                    customClass: {
                                        popup: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
                                        confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
                                        cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
                                    },
                                });
                                requestInProgress = false;
                            },
                            error: function (error) {
                                Swal.fire({
                                    title: 'Erreur!',
                                    text: 'Il y a eu une erreur lors de l\'assignation du rôle.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    theme: 'dark',
                                    color: '#ffffff',
                                    background: '#000000',
                                    showClass: {
                                        popup: 'swal2-show',
                                        backdrop: 'swal2-backdrop-show',
                                        icon: 'swal2-icon-show'
                                    },
                                    hideClass: {
                                        popup: 'swal2-hide',
                                        backdrop: 'swal2-backdrop-hide',
                                        icon: 'swal2-icon-hide'
                                    },
                                    customClass: {
                                        popup: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
                                        confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
                                        cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
                                    },
                                });
                                requestInProgress = false;
                            }
                        });
                    });
                },
                error: function (error) {
                    console.error("There was an error fetching roles and permissions:", error);
                }
            })
        }

        function getRolesAndPermissions() {
            $.ajax({
                url: '/roles-permissions',
                method: "GET",
                success: function (response) {
                    var roles = response.roles;
                    var permissions = response.permissions;
                    var rolePermissions = response.rolePermissions;

                    // Create the table header with roles
                    var header =
                        '<tr class="dark:bg-neutral-800 px-6 py-3 dark:border-gray-700 border-b"><th></th>';
                    roles.forEach(function (role) {
                        header +=
                            '<th class="px-6 py-4 font-semibold text-center"><a href="#" class="hover:bg-gray-600 px-8 py-2" data-role-id="' +
                            role.id + '" data-role-name="' + role.name + '">' + role.name + '</a></th>';
                    });
                    header += '</tr>';
                    $('#rolesTable thead').html(header);

                    // Create the table body with permissions and checkboxes
                    var body = '';

                    // Function to evaluate and update the "manage all" checkbox
                    function updateManageAllCheckbox(roleId) {
                        var allChecked = true;
                        var manageAllCheckbox = null;

                        $('input.permission-checkbox[data-role-id="' + roleId + '"]').each(function () {
                            if ($(this).closest('tr').hasClass('manage-all-permission')) {
                                manageAllCheckbox = $(this);
                            } else {
                                if (!$(this).is(':checked')) {
                                    allChecked = false;
                                }
                            }
                        });

                        if (manageAllCheckbox) {
                            manageAllCheckbox.prop('checked', allChecked);
                        }
                    }

                    // Function to handle checking or unchecking all permissions
                    function toggleAllPermissions(roleId, checkAll) {
                        $('input.permission-checkbox[data-role-id="' + roleId + '"]').each(function () {
                            if (!$(this).closest('tr').hasClass('manage-all-permission')) {
                                $(this).prop('checked', checkAll);
                            }
                        });

                        if (manageAllCheckbox) {
                            manageAllCheckbox.prop('checked', allChecked);
                        }
                    }
                    // Function to handle checking or unchecking all permissions
                    function toggleAllPermissions(roleId, checkAll) {
                        $('input.permission-checkbox[data-role-id="' + roleId + '"]').each(function () {
                            if (!$(this).closest('tr').hasClass('manage-all-permission')) {
                                $(this).prop('checked', checkAll);
                            }
                        });
                    }

                    // Initial rendering of the table
                    permissions.forEach(function (permission) {
                        var isManageAll = (permission.name === 'gérer tout');
                        var rowClass = isManageAll ? 'manage-all-permission' : '';

                        body += '<tr class="' + rowClass + '"><th class="w-full text-md text-start"><a href="#" class="px-8 py-2" data-permission-id="' +
                            permission.id + '" data-permission-name="' + permission.name +
                            '">' +
                            permission.name + '</a></th>';
                        roles.forEach(function (role) {
                            var checked = rolePermissions[role.id] &&
                                rolePermissions[
                                    role.id].includes(permission.id) ? 'checked' :
                                '';
                            body +=
                                '<td class="px-6 py-3 text-center"><input type="checkbox" class="w-5 h-5 accent-indigo-500 permission-checkbox" data-role-id="' +
                                role.id + '" data-permission-id="' + permission.id +
                                '" ' + checked + '></td>';
                        });
                        body += '</tr>';
                    });

                    $('#rolesTable tbody').html(body);

                    $('#rolesTable').on('click', 'a[data-permission-id]', function (event) {
                        event.preventDefault();
                        var permissionId = $(this).data('permission-id');
                        var permissionName = $(this).text();

                        const urlPermissionUpdate = $('#rolesTable').data('update-permission-url').replace('__ID__', permissionId);
                        const urlPermissionDestroy = $('#rolesTable').data('destroy-permission-url').replace('__ID__', permissionId);

                        Swal.fire({
                            title: 'Permission : ' + permissionName,
                            html: '<input id="permission-name" class="dark:bg-gray-700 px-4 py-4 rounded-xl text-gray-400 dark:text-gray-200" type="text" value="' +
                                permissionName + '">',
                            text: 'Que voulez-vous faire?',
                            icon: 'question',
                            theme: 'dark',
                            color: '#ffffff',
                            background: '#000000',
                            showClass: {
                                popup: 'swal2-show',
                                backdrop: 'swal2-backdrop-show',
                                icon: 'swal2-icon-show'
                            },
                            hideClass: {
                                popup: 'swal2-hide',
                                backdrop: 'swal2-backdrop-hide',
                                icon: 'swal2-icon-hide'
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Mettre à jour',
                            cancelButtonText: 'Supprimer',
                            customClass: {
                                popup: 'bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
                                confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
                                cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
                            },
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Handle update action
                                var newPermissionName = $('#permission-name').val();
                                // Send AJAX request to update permission
                                $.ajax({
                                    url: urlPermissionUpdate,
                                    method: 'PUT',
                                    data: {
                                        _token: $('meta[name="csrf-token"]').attr('content'),
                                        name: newPermissionName,
                                    },
                                    success: function (response) {
                                        console.log(
                                            'Permission updated successfully'
                                        );
                                        Swal.fire({
                                            title: 'Succès!',
                                            text: response.message,
                                            icon: 'success',
                                            timer: 2000,
                                            timerProgressBar: true,
                                            theme: 'dark',
                                            color: '#ffffff',
                                            background: '#000000',
                                            showClass: {
                                                popup: 'swal2-show',
                                                backdrop: 'swal2-backdrop-show',
                                                icon: 'swal2-icon-show'
                                            },
                                            hideClass: {
                                                popup: 'swal2-hide',
                                                backdrop: 'swal2-backdrop-hide',
                                                icon: 'swal2-icon-hide'
                                            },
                                            customClass: {
                                                popup: 'bg-gray-800 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
                                                confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
                                                cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
                                            },
                                        });
                                        getRolesAndPermissions();
                                    },
                                    error: function (error) {
                                        console.error(
                                            'Error updating permission:',
                                            error);
                                    }
                                });
                            } else if (result.dismiss === Swal.DismissReason
                                .cancel) {
                                // Send AJAX request to delete permission
                                $.ajax({
                                    url: urlPermissionDestroy,
                                    method: 'DELETE',
                                    data: {
                                        _token: $('meta[name="csrf-token"]').attr('content'),
                                    },
                                    success: function (response) {
                                        console.log(
                                            'Permission deleted successfully'
                                        );
                                        Swal.fire({
                                            title: 'Succès!',
                                            text: response
                                                .message,
                                            icon: 'success',
                                            timer: 2000,
                                            timerProgressBar: true,
                                            theme: 'dark',
                                            color: '#ffffff',
                                            background: '#000000',
                                            showClass: {
                                                popup: 'swal2-show',
                                                backdrop: 'swal2-backdrop-show',
                                                icon: 'swal2-icon-show'
                                            },
                                            hideClass: {
                                                popup: 'swal2-hide',
                                                backdrop: 'swal2-backdrop-hide',
                                                icon: 'swal2-icon-hide'
                                            },
                                            customClass: {
                                                popup: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
                                                confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
                                                cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
                                            },
                                        });
                                        getRolesAndPermissions();
                                    },
                                    error: function (error) {
                                        console.error(
                                            'Error deleting permission:',
                                            error);
                                    }
                                });
                            }
                        });
                    });

                    $('#rolesTable').on('click', 'a[data-role-id]', function (event) {
                        event.preventDefault();
                        var roleId = $(this).data('role-id');
                        var roleName = $(this).text();

                        const urlRoleUpdate = $('#rolesTable').data('update-url').replace('__ID__', roleId);
                        const urlRoleDestroy = $('#rolesTable').data('destroy-url').replace('__ID__', roleId);

                        Swal.fire({
                            title: 'Role : ' + roleName,
                            html: '<input id="role-name" type="text" class="dark:bg-gray-700 px-4 py-4 rounded-xl text-gray-800 dark:text-gray-200" value="' +
                                roleName + '">',
                            text: 'Que voulez-vous faire?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Mettre à jour',
                            cancelButtonText: 'Supprimer',
                            theme: 'dark',
                            color: '#ffffff',
                            background: '#000000',
                            showClass: {
                                popup: 'swal2-show',
                                backdrop: 'swal2-backdrop-show',
                                icon: 'swal2-icon-show'
                            },
                            hideClass: {
                                popup: 'swal2-hide',
                                backdrop: 'swal2-backdrop-hide',
                                icon: 'swal2-icon-hide'
                            },
                            customClass: {
                                popup: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
                                confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
                                cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
                            },
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Handle update action
                                var newRoleName = $('#role-name').val();
                                // Send AJAX request to update role
                                $.ajax({
                                    url: urlRoleUpdate,
                                    method: 'PUT',
                                    data: {
                                        _token: $('meta[name="csrf-token"]').attr('content'),
                                        name: newRoleName,
                                    },
                                    success: function (response) {
                                        console.log(
                                            'Role updated successfully'
                                        );
                                        Swal.fire({
                                            title: 'Succès!',
                                            text: response
                                                .message,
                                            icon: 'success',
                                            timer: 2000,
                                            timerProgressBar: true,
                                            theme: 'dark',
                                            color: '#ffffff',
                                            background: '#000000',
                                            showClass: {
                                                popup: 'swal2-show',
                                                backdrop: 'swal2-backdrop-show',
                                                icon: 'swal2-icon-show'
                                            },
                                            hideClass: {
                                                popup: 'swal2-hide',
                                                backdrop: 'swal2-backdrop-hide',
                                                icon: 'swal2-icon-hide'
                                            },
                                            customClass: {
                                                popup: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
                                                confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
                                                cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
                                            },
                                        });
                                        getRolesAndPermissions();
                                    },
                                    error: function (error) {
                                        console.error(
                                            'Error updating role:',
                                            error);
                                    }
                                });
                            } else if (result.dismiss === Swal.DismissReason
                                .cancel) {
                                // Send AJAX request to delete role
                                $.ajax({
                                    url: urlRoleDestroy,
                                    method: 'DELETE',
                                    data: {
                                        _token: $('meta[name="csrf-token"]').attr('content'),
                                    },
                                    success: function (response) {
                                        console.log(
                                            'Role deleted successfully'
                                        );
                                        Swal.fire({
                                            title: 'Succès!',
                                            text: response
                                                .message,
                                            icon: 'success',
                                            timer: 2000,
                                            timerProgressBar: true,
                                            theme: 'dark',
                                            color: '#ffffff',
                                            background: '#000000',
                                            showClass: {
                                                popup: 'swal2-show',
                                                backdrop: 'swal2-backdrop-show',
                                                icon: 'swal2-icon-show'
                                            },
                                            hideClass: {
                                                popup: 'swal2-hide',
                                                backdrop: 'swal2-backdrop-hide',
                                                icon: 'swal2-icon-hide'
                                            },
                                            customClass: {
                                                popup: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
                                                confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
                                                cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
                                            },
                                        });
                                        getRolesAndPermissions();
                                    },
                                    error: function (error) {
                                        console.error(
                                            'Error deleting role:',
                                            error);
                                    }
                                });
                            }
                        });
                    });

                    // Attach change event listeners to checkboxes
                    var requestInProgress = false;

                    // Attach change event listeners to checkboxes
                    var requestInProgress = false;

                    $('#rolesTable').on('change', 'input.permission-checkbox', function () {
                        if (requestInProgress) {
                            return;
                        }

                        requestInProgress = true;

                        var roleId = $(this).data('role-id');
                        var permissionId = $(this).data('permission-id');
                        var checked = $(this).is(':checked');

                        // If the "manage all" checkbox is changed, check/uncheck all other permissions
                        if ($(this).closest('tr').hasClass('manage-all-permission')) {
                            toggleAllPermissions(roleId, checked);
                        } else {
                            // Otherwise, update the "manage all" checkbox based on individual permissions
                            updateManageAllCheckbox(roleId);
                        }

                        // Your existing AJAX logic to update permissions on the server
                        $.ajax({
                            url: '/roles-permissions/update',
                            method: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                role_id: roleId,
                                permission_id: permissionId,
                                assign: checked
                            },
                            success: function (response) {
                                Swal.fire({
                                    title: 'Succès!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    timerProgressBar: true,
                                    theme: 'dark',
                                    color: '#ffffff',
                                    background: '#000000',
                                    showClass: {
                                        popup: 'swal2-show',
                                        backdrop: 'swal2-backdrop-show',
                                        icon: 'swal2-icon-show'
                                    },
                                    hideClass: {
                                        popup: 'swal2-hide',
                                        backdrop: 'swal2-backdrop-hide',
                                        icon: 'swal2-icon-hide'
                                    },
                                    customClass: {
                                        popup: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
                                        confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
                                        cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
                                    },
                                });
                                requestInProgress = false;
                            },
                            error: function (error) {
                                Swal.fire({
                                    title: 'Erreur!',
                                    text: 'Il y a eu une erreur lors de l\'assignation de la permission.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    theme: 'dark',
                                    color: '#ffffff',
                                    background: '#000000',
                                    showClass: {
                                        popup: 'swal2-show',
                                        backdrop: 'swal2-backdrop-show',
                                        icon: 'swal2-icon-show'
                                    },
                                    hideClass: {
                                        popup: 'swal2-hide',
                                        backdrop: 'swal2-backdrop-hide',
                                        icon: 'swal2-icon-hide'
                                    },
                                    customClass: {
                                        popup: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
                                        confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
                                        cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
                                    },
                                });
                                requestInProgress = false;
                            }
                        });
                    });
                },
                error: function (error) {
                    console.error("There was an error fetching roles and permissions:", error);
                }
            })
        }
    </script>
    @fluxScripts
</body>

</html>
