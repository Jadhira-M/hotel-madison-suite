<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");
include("../config/db_utils.php");

$usuarioId = (int) $_SESSION["id_usuario"];
$tieneTotal = db_column_exists($conn, "reservas", "total");
$tieneNoches = db_column_exists($conn, "reservas", "noches");
$totalExpr = $tieneTotal
    ? "COALESCE(NULLIF(r.total, 0), GREATEST(DATEDIFF(r.fecha_salida, r.fecha_ingreso), 1) * h.precio)"
    : "GREATEST(DATEDIFF(r.fecha_salida, r.fecha_ingreso), 1) * h.precio";
$nochesExpr = $tieneNoches
    ? "COALESCE(NULLIF(r.noches, 0), GREATEST(DATEDIFF(r.fecha_salida, r.fecha_ingreso), 1))"
    : "GREATEST(DATEDIFF(r.fecha_salida, r.fecha_ingreso), 1)";

$stmt = $conn->prepare("SELECT r.*, h.nombre AS habitacion, {$nochesExpr} AS noches_calc, {$totalExpr} AS total_calc
    FROM reservas r
    INNER JOIN habitaciones h ON h.id = r.habitacion_id
    WHERE r.usuario_id = ?
    ORDER BY r.fecha_ingreso DESC, r.id DESC");
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$reservas = db_fetch_all($stmt->get_result());

include("../includes/header.php");
?>

<body>
<?php include("../includes/navbar.php"); ?>

<main class="container py-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <p class="section-kicker">Mi cuenta</p>
            <h1 class="fw-bold">Historial de Reservas</h1>
        </div>
        <a href="dashboard.php" class="btn btn-outline-warning">Volver a mi cuenta</a>
    </div>

    <div class="card shadow">
        <div class="card-body table-responsive">
            <?php if (!$reservas): ?>
                <p class="mb-0">Todavia no tienes reservas registradas.</p>
            <?php else: ?>
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Habitacion</th>
                            <th>Ingreso</th>
                            <th>Salida</th>
                            <th>Noches</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $reserva): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reserva["habitacion"]); ?></td>
                                <td><?php echo htmlspecialchars($reserva["fecha_ingreso"]); ?></td>
                                <td><?php echo htmlspecialchars($reserva["fecha_salida"]); ?></td>
                                <td><?php echo (int) $reserva["noches_calc"]; ?></td>
                                <td>S/ <?php echo number_format((float) $reserva["total_calc"], 0); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($reserva["estado"])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include("../includes/footer.php"); ?>
</body>
</html>
