<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");
include("../config/db_utils.php");
include("layout/header.php");
include("layout/sidebar.php");

function adminTableExists($conn, $table)
{
    $database = $conn->query("SELECT DATABASE() AS db")->fetch_assoc()["db"] ?? "";
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = ?
          AND TABLE_NAME = ?
    ");
    $stmt->bind_param("ss", $database, $table);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return (int) ($row["total"] ?? 0) > 0;
}

$mensaje = "";
$error = "";
$tablaLista = adminTableExists($conn, "resenas");

if ($tablaLista && $_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int) ($_POST["id"] ?? 0);
    $accion = $_POST["accion"] ?? "";

    if ($id > 0 && in_array($accion, ["publicar", "ocultar"], true)) {
        $estado = $accion === "publicar" ? "publicado" : "oculto";
        $stmt = $conn->prepare("UPDATE resenas SET estado = ? WHERE id = ?");
        $stmt->bind_param("si", $estado, $id);
        $mensaje = $stmt->execute() ? "La reseña fue actualizada." : "";
        $error = $mensaje ? "" : "No se pudo actualizar la reseña.";
    }
}

$resenas = [];
$stats = ["total" => 0, "publicadas" => 0, "ocultas" => 0, "promedio" => 0];

if ($tablaLista) {
    $statsResult = $conn->query("SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN estado = 'publicado' THEN 1 ELSE 0 END) AS publicadas,
        SUM(CASE WHEN estado <> 'publicado' THEN 1 ELSE 0 END) AS ocultas,
        COALESCE(AVG(puntuacion), 0) AS promedio
        FROM resenas");
    $stats = $statsResult ? $statsResult->fetch_assoc() : $stats;

    $result = $conn->query("SELECT * FROM resenas ORDER BY creado_en DESC, id DESC");
    $resenas = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2>Administración de Reseñas</h2>
            <p class="text-muted mb-0">Modera opiniones publicadas por los clientes.</p>
        </div>
        <a href="../resenas.php" class="btn btn-warning" target="_blank">
            <i class="bi bi-box-arrow-up-right"></i> Ver página pública
        </a>
    </div>

    <?php if ($mensaje): ?><div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-warning"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <?php if (!$tablaLista): ?>
        <div class="alert alert-warning">
            La tabla de reseñas aún no existe. Ejecuta <strong>database/resenas_dinamicas.sql</strong> en phpMyAdmin.
        </div>
    <?php else: ?>
        <div class="row g-3 mb-4">
            <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6>Total</h6><strong class="fs-2"><?php echo (int) $stats["total"]; ?></strong></div></div></div>
            <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6>Publicadas</h6><strong class="fs-2"><?php echo (int) $stats["publicadas"]; ?></strong></div></div></div>
            <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6>Ocultas</h6><strong class="fs-2"><?php echo (int) $stats["ocultas"]; ?></strong></div></div></div>
            <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6>Promedio</h6><strong class="fs-2"><?php echo number_format((float) $stats["promedio"], 1); ?></strong></div></div></div>
        </div>

        <div class="card shadow">
            <div class="card-body table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Cliente</th>
                            <th>Puntuación</th>
                            <th>Título</th>
                            <th>Comentario</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resenas as $resena): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($resena["nombre"]); ?></strong><br>
                                    <small><?php echo htmlspecialchars($resena["correo"] ?? ""); ?></small>
                                </td>
                                <td><?php echo number_format((float) $resena["puntuacion"], 1); ?></td>
                                <td><?php echo htmlspecialchars($resena["titulo"] ?? ""); ?></td>
                                <td style="max-width:360px"><?php echo htmlspecialchars($resena["comentario"]); ?></td>
                                <td>
                                    <?php if (($resena["estado"] ?? "") === "publicado"): ?>
                                        <span class="badge bg-success">Publicada</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Oculta</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($resena["creado_en"]); ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo (int) $resena["id"]; ?>">
                                        <input type="hidden" name="accion" value="<?php echo ($resena["estado"] ?? "") === "publicado" ? "ocultar" : "publicar"; ?>">
                                        <button class="btn btn-warning btn-sm" type="submit">
                                            <?php echo ($resena["estado"] ?? "") === "publicado" ? "Ocultar" : "Publicar"; ?>
                                        </button>
                                    </form>
                                    <a href="eliminar_resena.php?id=<?php echo (int) $resena["id"]; ?>" class="btn btn-danger btn-sm btn-eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include("layout/footer.php"); ?>
