// Global variables for DataTables instances
let requestsTable;

function initializeDataTables() {
    if (document.getElementById("requestsTable")) {
        if (requestsTable) {
            requestsTable.destroy();
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
                        panes: [
                            {
                                header: 'Statut',
                                options: [
                                    {
                                        label: 'En attente',
                                        value: function (rowData, rowIdx) {
                                            return rowData[5] && rowData[5] == 'pending';
                                        }
                                    },
                                    {
                                        label: 'En cours de traitement',
                                        value: function (rowData, rowIdx) {
                                            return rowData[5] && rowData[5] == 'in_progress';
                                        }
                                    },
                                    {
                                        label: 'Annulée',
                                        value: function (rowData, rowIdx) {
                                            return rowData[5] && rowData[5] == 'cancelled';
                                        }
                                    },
                                    {
                                        label: 'Déjà traitée',
                                        value: function (rowData, rowIdx) {
                                            return rowData[5] && rowData[5] == 'processed';
                                        }
                                    }
                                ],
                            }
                        ],
                    }
                }
            },
            columnDefs: [
                {
                    orderable: false,
                    searchPanes: {
                        show: false,
                    },
                    targets: [0]
                },
            ],
        });
    }
}

// Initialize DataTables on page load
initializeDataTables();


