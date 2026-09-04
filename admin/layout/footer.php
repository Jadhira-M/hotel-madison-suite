</main>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
$archivo = basename($_SERVER['PHP_SELF'], ".php");
$jsPath = __DIR__ . "/../../assets/js/" . $archivo . ".js";
$jsVersion = is_file($jsPath) ? filemtime($jsPath) : time();
?>

<script src="../assets/js/<?php echo $archivo; ?>.js?v=<?php echo $jsVersion; ?>"></script>

<script>
document.addEventListener("click", function (event) {
    const reservaConfirm = event.target.closest(".js-reserva-confirm");
    const reservaCancel = event.target.closest(".js-reserva-cancel");
    const deleteLink = event.target.closest(".btn-eliminar");

    if (!reservaConfirm && !reservaCancel && !deleteLink) {
        return;
    }

    event.preventDefault();

    const enlace = reservaConfirm || reservaCancel || deleteLink;
    const href = enlace.getAttribute("href") || "";
    let config;

    if (reservaConfirm) {
        config = {
            title: "Confirmar reserva",
            text: "La reserva pasara a estado confirmada.",
            confirmButtonText: "Si, confirmar"
        };
    } else if (reservaCancel) {
        config = {
            title: "Cancelar reserva",
            text: "Esta accion cambiara la reserva a cancelada.",
            confirmButtonText: "Si, cancelar"
        };
    } else if (href.includes("eliminar_usuario.php")) {
        config = {
            title: "Eliminar usuario",
            text: "Esta accion eliminara la cuenta seleccionada y no se puede deshacer.",
            confirmButtonText: "Si, eliminar"
        };
    } else if (href.includes("eliminar_resena.php")) {
        config = {
            title: "Eliminar reseña",
            text: "Esta accion eliminara la opinion seleccionada y no se puede deshacer.",
            confirmButtonText: "Si, eliminar"
        };
    } else if (href.includes("eliminar_galeria.php")) {
        config = {
            title: "Eliminar imagen",
            text: "Esta accion eliminara la imagen de la galeria y no se puede deshacer.",
            confirmButtonText: "Si, eliminar"
        };
    } else {
        config = {
            title: "Eliminar habitacion",
            text: "Esta accion no se puede deshacer.",
            confirmButtonText: "Si, eliminar"
        };
    }

    if (!window.Swal) {
        if (window.confirm(config.title + "\n\n" + config.text)) {
            window.location.href = enlace.getAttribute("href");
        }
        return;
    }

    Swal.fire({
        title: config.title,
        text: config.text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: config.confirmButtonText,
        cancelButtonText: "Cancelar",
        customClass: {
            popup: "admin-swal"
        }
    }).then(function (result) {
        if (result.isConfirmed) {
            window.location.href = enlace.getAttribute("href");
        }
    });
});
</script>

</body>
</html>
