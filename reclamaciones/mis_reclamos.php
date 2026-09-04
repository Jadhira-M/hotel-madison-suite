<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php?redirect=../reclamaciones/mis_reclamos.php");
    exit();
}

include("../config/conexion.php");
include("../config/db_utils.php");

$usuarioId = (int) $_SESSION["id_usuario"];
$tieneCodigo = db_column_exists($conn, "reclamaciones", "codigo");
$tieneNombre = db_column_exists($conn, "reclamaciones", "nombre");
$selectCodigo = $tieneCodigo ? "codigo" : "'' AS codigo";
$selectNombre = $tieneNombre ? "nombre" : "'' AS nombre";

$stmt = $conn->prepare("SELECT id, {$selectCodigo}, {$selectNombre}, tipo, detalle, fecha, estado
    FROM reclamaciones
    WHERE usuario_id = ?
    ORDER BY id DESC");
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$reclamos = db_fetch_all($stmt->get_result());

include("../includes/header.php");
?>

<body>
<?php include("../includes/navbar.php"); ?>

<main class="container py-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <p class="section-kicker">Libro de reclamaciones</p>
            <h1 class="fw-bold">Mis Reclamos</h1>
        </div>
        <a href="libro.php" class="btn btn-warning">Nuevo Reclamo</a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <?php if (!$reclamos): ?>
                <p class="mb-0">Todavia no tienes reclamos registrados.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Codigo</th>
                                <th>Tipo</th>
                                <th>Detalle</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reclamos as $reclamo): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reclamo["codigo"] ?: "REC-" . str_pad((string) $reclamo["id"], 3, "0", STR_PAD_LEFT)); ?></td>
                                    <td><?php echo htmlspecialchars($reclamo["tipo"]); ?></td>
                                    <td><?php echo htmlspecialchars($reclamo["detalle"]); ?></td>
                                    <td><?php echo htmlspecialchars($reclamo["fecha"]); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($reclamo["estado"])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include("../includes/footer.php"); ?>
</body>
</html>
