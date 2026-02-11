// Global variables for DataTables instances
let usersTableInstance;
let rolesTableInstance;
let usersRolesTableInstance;

function initializeDataTables() {
    if (document.getElementById("users-table")) {
        if (usersTableInstance) {
            usersTableInstance.destroy();
        }
        usersTableInstance = $('#users-table').DataTable({
            paging: true,
            pageLength: 10,
            lengthChange: false,
            info: true,
            autoWidth: false,
            language: {
                search: "Rechercher:",
                paginate: {
                    next: "Suivant",
                    previous: "Précédent"
                },
                info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées"
            }
        });
    }
    if (document.getElementById("rolesTable")) {
        if (rolesTableInstance) {
            rolesTableInstance.destroy();
        }
        rolesTableInstance = $('#rolesTable').DataTable({
            paging: false,
            lengthChange: false,
            info: false,
            searching: false,
            ordering: false,
            autoWidth: false
        });
    }
    if (document.getElementById("usersRolesTable")) {
        if (usersRolesTableInstance) {
            usersRolesTableInstance.destroy();
        }
        usersRolesTableInstance = $('#usersRolesTable').DataTable({
            paging: false,
            lengthChange: false,
            info: false,
            searching: false,
            ordering: false,
            autoWidth: false
        });
    }
}

// Initialize DataTables on page load
initializeDataTables();

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function () {
    getRolesAndPermissions();
    getUsersRoles();
});

$('#newUserButton').click(function (e) {
    e.preventDefault();

    // Trigger SweetAlert with input
    Swal.fire({
        title: 'Créer un utilisateur',
        html: `
                <form id="new-user-form" class="p-4 md:p-5" method="post" action="{{ route('users.store') }}">
                    <div class="grid gap-4 mb-4 grid-cols-2 text-left">
                        <div class="col-span-2 flex items-center">
                            <label for="name" class="block w-full mb-2 text-sm md:text-base font-medium text-black dark:text-white">Nom d'utilisateur</label>
                            <input type="text" name="name" id="name" class="border border-gray-300 text-gray-800 text-sm md:text-base rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="John Doe" required="">
                        </div>
                        <div class="col-span-2 flex items-center">
                            <label for="email" class="block w-full mb-2 text-sm md:text-base font-medium text-black dark:text-white">Adresse mail</label>
                            <input type="email" name="email" id="email" class="border border-gray-300 text-gray-800 text-sm md:text-base rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="exemple@exemple.com" required="">
                        </div>
                        <div class="col-span-2 flex items-center">
                            <label for="password" class="block w-full mb-2 text-sm md:text-base font-medium text-black dark:text-white">Mot de passe</label>
                            <input type="text" name="password" id="password" class="w-full border border-gray-300 text-gray-800 text-sm md:text-base rounded-lg focus:ring-primary-600 focus:border-primary-600 block p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Tapez un mot de passe" required="">
                        </div>
                        <div class="flex space-x-3">
                            <input id="mail" name="mail" type="checkbox" class="md:w-5 md:h-5 w-4 h-4 text-black dark:text-[#e38407] border-gray-300 rounded focus:ring-[#e38407] dark:focus:ring-[#e38407] dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="mail" class="block text-sm md:text-base font-medium text-black dark:text-white">Notifier par mail</label>
                        </div>
                    </div>
                </form>
            `,
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
        confirmButtonText: 'Ajouter un nouvel utilisateur',
        cancelButtonText: 'Annuler',
        allowOutsideClick: false, // Empêche de fermer en cliquant en dehors
        preConfirm: () => {
            const formData = new FormData($('#new-user-form')[0]);

            // Vérifier si les champs sont vides
            const name = formData.get('name');
            const email = formData.get('email');
            const password = formData.get('password');

            if (!name || !email || !password) {
                Swal.showValidationMessage('Veuillez remplir tous les champs requis.');
                return false;
            }

            formData.append('_token', $('meta[name="csrf-token"]').attr('content')); // Ajouter le token CSRF
            return {
                name: name,
                email: email,
                password: password,
                mail: formData.get('mail'),
                _token: formData.get('_token') // Inclure le token CSRF
            };
        },
        customClass: {
            popup: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
            confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
            cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
        },
    }).then((result) => {
        if (result.isConfirmed) {
            // Faire la requête AJAX pour ajouter l'utilisateur
            $.ajax({
                url: "{{ route('users.store') }}",
                method: 'POST',
                data: result.value,
                success: function (response) {
                    Swal.fire({
                        title: 'Utilisateur créé avec succès !',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        background: '#132329', // Fond sombre
                        color: '#fff', // Couleur du texte blanche
                        iconColor: '#ffdd57',
                    });
                    getUsersRoles(); // Mettre à jour la liste des utilisateurs et rôles
                },
                error: function (error) {
                    Swal.fire({
                        title: 'Erreur',
                        text: error.responseJSON?.message ||
                            'Une erreur est survenue lors de la création de l\'utilisateur.',
                        icon: 'error',
                        background: '#132329', // Fond sombre
                        color: '#fff', // Couleur du texte blanche
                        iconColor: '#ffdd57',
                    });
                }
            });
        }
    });
});

$('#newPermissionButton').click(function (e) {
    e.preventDefault();

    // Trigger SweetAlert with input
    Swal.fire({
        title: 'Créer une permission',
        input: 'text',
        inputPlaceholder: 'Entrez le nom de la permission',
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
        confirmButtonText: 'Créer',
        cancelButtonText: 'Annuler',
        inputValidator: (value) => {
            if (!value) {
                return 'Vous devez entrer un nom de permission !';
            }
        },
        customClass: {
            input: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
            popup: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
            confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
            cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
        },
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Make AJAX request to add the permission
            $.ajax({
                url: "{{ route('permissions.store') }}",
                method: 'POST',
                data: {
                    name: result.value,
                    _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token for security
                },
                success: function (response) {
                    Swal.fire({
                        title: 'Permission créée avec succès !',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
                            confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
                            cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
                        },
                    });
                    getRolesAndPermissions();
                },
                error: function (error) {
                    Swal.fire({
                        title: 'Erreur',
                        text: xhr.responseJSON.message ||
                            'Une erreur est survenue lors de la création de la permission.',
                        icon: 'error',
                        customClass: {
                            popup: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
                            confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
                            cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
                        },
                    });
                }
            });
        }
    });
});

$('#newRoleButton').click(function (e) {
    e.preventDefault();

    // Trigger SweetAlert with input
    Swal.fire({
        title: 'Créer un rôle',
        input: 'text',
        inputPlaceholder: 'Entrez le nom du nouveau rôle',
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
        confirmButtonText: 'Créer',
        cancelButtonText: 'Annuler',
        inputValidator: (value) => {
            if (!value) {
                return 'Vous devez entrer un nom de rôle !';
            }
        },
        customClass: {
            input: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
            popup: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
            confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
            cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
        },
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Make AJAX request to add the permission
            $.ajax({
                url: "{{ route('roles.store') }}",
                method: 'POST',
                data: {
                    name: result.value,
                    _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token for security
                },
                success: function (response) {
                    Swal.fire({
                        title: 'Rôle créé avec succès !',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
                            confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
                            cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
                        },
                    });
                    getRolesAndPermissions();
                },
                error: function (error) {
                    Swal.fire({
                        title: 'Erreur',
                        text: xhr.responseJSON.message ||
                            'Une erreur est survenue lors de la création de la permission.',
                        icon: 'error',
                        customClass: {
                            popup: 'bg-gray-200 dark:bg-gray-800 text-black dark:text-white rounded-lg shadow-lg', // Classes Tailwind pour le popup
                            confirmButton: 'bg-[#e38407] hover:bg-[#e38407] text-white font-bold py-2 px-4 rounded', // Bouton de confirmation
                            cancelButton: 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded' // Bouton d'annulation
                        },
                    });
                }
            });
        }
    });
});

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
