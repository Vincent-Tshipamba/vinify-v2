function showUserProfile(user_id, name, email, created_at, user_last_activity, cache_exists) {
    event.preventDefault();
    Swal.fire({
        title: 'Informations de l\'utilisateur',
        html: `
    <section class="w-full p-4 mt-4 border-gray-200 rounded-xl gap-12">
        <div class="bg-white dark:bg-gray-900 shadow-lg dark:shadow-lg dark:shadow-gray-500/20 p-4 mb-4 rounded-lg border dark:border-gray-500 text-center">
            <div class="flex justify-center">
                <img loading="lazy" src="/img/profil.jpg" alt="" class="w-28 h-28 rounded-full border border-gray-900 dark:border-gray-500 object-cover">
            </div>
            <div class="mb-4">
                <h2 class="text-4xl mb-2 font-extrabold leading-none tracking-tight text-gray-700 md:text-5xl lg:text-5xl dark:text-white">${name}</h2>
                <p class="text-sm text-gray-400">${email}</p>
            </div>
            <div class=" flex justify-center gap-5 text-center">
                <div>
                    <p class="mb-4 text-lg leading-none tracking-tight text-gray-700 dark:text-white">
                        ${cache_exists ? `
                                                                                                    <div class="flex items-center">
                                                                                                        <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div> En ligne
                                                                                                    </div>
                                                                                                ` : `
                                                                                                    <p class="text-gray-500">
                                                                                                        <span id="diff_last_time">Hors ligne</span>
                                                                                                    </p>`
            }
                    </p>
                </div>
            </div>
        </div>
        <div class="border dark:border-gray-500 bg-white dark:bg-gray-900 shadow-lg dark:shadow-lg dark:shadow-gray-500/20 p-4 mb-4 rounded-lg text-lg font-normal text-gray-500 lg:text-sm dark:text-gray-400">
            <div class="mb-1.5 text-4xl font-extrabold leading-none tracking-tight text-gray-700 md:text-2xl lg:text-2xl dark:text-white">
                <h2>Coordonnées utilisateur</h2>
            </div>
            <hr class="my-4">
                <div class="text-left flex">
                    <p class="w-1/2">Nom d'utilisateur :</p><span class="font-bold">${name}</span>
                </div>
                <hr class="my-4">
                    <div class="text-left flex">
                        <p class="w-1/2">Adresse mail:</p><span class="font-bold">${email}</span>
                    </div>
                    <hr class="my-4">
                        <div class="text-left flex">
                            <p class="w-1/2">Date de création:</p><span class="font-bold">${new Date(created_at).toLocaleDateString()}</span>
                        </div>
                    </div>
                </section>
                `,
        allowOutsideClick: false,
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
    })
}

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
