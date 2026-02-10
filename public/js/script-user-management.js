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

function changeUserStatus(userId) {
    event.preventDefault();
    var isActive = event.target.checked;
    $.ajax({
        type: "put",
        url: 'admin/users/change-status',
        data: {
            userId: userId,
            isActive: isActive
        },
        dataType: "json",
        success: function (response) {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: response.message
            });
        },
        error: function (error) {
            console.error(
                'Error changing user status:',
                error);
        }
    });
}

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

