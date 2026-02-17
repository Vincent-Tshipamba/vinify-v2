function initializeDataTables() {
    const tableElement = document.getElementById("requestsTable");
    if (!tableElement) return;

    if ($.fn.DataTable.isDataTable('#requestsTable')) {
        $('#requestsTable').DataTable().clear().destroy();
    }

    requestsTable = $('#requestsTable').DataTable({
        paging: true,
        lengthChange: true,
        info: true,
        searching: true,
        ordering: true,
        scrollY: '600px',
        autoWidth: true,
        language: {
            search: "Rechercher : ",
            paginate: {
                next: "Suivant",
                previous: "Précédent"
            },
            lengthMenu: "Afficher _MENU_ entrées",
            loadingRecords: "Chargement...",
            infoEmpty: 'Aucune demande d\'analyse jusque-là !',
            zeroRecords: 'Aucune demande d\'analyse trouvée, désolé !',
            info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées"
        },
        layout: {
            topStart: {
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            },
            top1: {
                searchPanes: {
                    viewTotal: true,
                }
            }
        },
        columnDefs: [
            {
                orderable: false,
                searchPanes: {
                    show: false,
                },
                targets: [0,6]
            },
        ],
    });
}

// Initialize DataTables on page load
initializeDataTables();


