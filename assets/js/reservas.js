$(document).ready(function () {
    if ($('#tablaReservas').length && !$.fn.DataTable.isDataTable('#tablaReservas')) {
        $('#tablaReservas').DataTable({
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json"
            }
        });
    }
});
