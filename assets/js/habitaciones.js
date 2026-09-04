$(document).ready(function () {
    $('#tablaHabitaciones').DataTable({
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json"
        }
    });

    // La confirmacion para eliminar se maneja globalmente en admin/layout/footer.php.
});
