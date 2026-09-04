<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");
include("../config/db_utils.php");

function estadoTexto($estado)
{
    $labels = [
        "pendiente" => "Pendiente",
        "en_proceso" => "En Proceso",
        "atendida" => "Atendida",
    ];

    return $labels[$estado] ?? "Pendiente";
}

function normalizarReclamo($row)
{
    $codigo = ($row["codigo"] ?? "") ?: "REC-" . str_pad((string) $row["id"], 3, "0", STR_PAD_LEFT);
    $tipo = ($row["tipo"] ?? "") ?: "Reclamo";
    $bien = ($row["bien_servicio"] ?? "") ?: "Servicio del hotel";
    $nombre = ($row["nombre"] ?? "") ?: ($row["usuario_nombre"] ?? "Cliente");
    $pedido = $row["pedido"] ?? "";

    return [
        "id_db" => (int) $row["id"],
        "id" => $codigo,
        "titulo" => $tipo . " - " . $bien,
        "fecha" => $row["fecha"] ?? "",
        "habitacion" => "-",
        "prioridad" => ($row["prioridad"] ?? "") ?: "Media",
        "huesped" => $nombre,
        "tipo" => $tipo,
        "descripcion" => $row["detalle"] ?? "",
        "estado" => ($row["estado"] ?? "") ?: "pendiente",
        "detalle" => $pedido ? "Pedido del consumidor: " . $pedido : ($row["detalle"] ?? ""),
    ];
}

$mensaje = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reclamoId = (int) ($_POST["reclamo_id"] ?? 0);
    $nuevoEstado = $_POST["estado"] ?? "";
    $estadosPermitidos = ["pendiente", "en_proceso", "atendida"];

    if ($reclamoId > 0 && in_array($nuevoEstado, $estadosPermitidos, true)) {
        if (db_column_exists($conn, "reclamaciones", "fecha_atencion") && $nuevoEstado === "atendida") {
            $stmt = $conn->prepare("UPDATE reclamaciones SET estado = ?, fecha_atencion = NOW() WHERE id = ?");
        } else {
            $stmt = $conn->prepare("UPDATE reclamaciones SET estado = ? WHERE id = ?");
        }
        $stmt->bind_param("si", $nuevoEstado, $reclamoId);

        if ($stmt->execute()) {
            $mensaje = "El reclamo ahora está como " . estadoTexto($nuevoEstado) . ".";
        } else {
            $error = "No se pudo actualizar el reclamo.";
        }
    }
}

$filtro = $_GET["estado"] ?? "total";
$estadosFiltro = ["total", "pendiente", "en_proceso", "atendida"];
if (!in_array($filtro, $estadosFiltro, true)) {
    $filtro = "total";
}

$detalleId = $_GET["detalle"] ?? "";

$select = "SELECT r.*, u.nombre AS usuario_nombre
           FROM reclamaciones r
           LEFT JOIN usuarios u ON u.id = r.usuario_id";
$params = [];
$types = "";
if ($filtro !== "total") {
    $select .= " WHERE r.estado = ?";
    $params[] = $filtro;
    $types .= "s";
}
$select .= " ORDER BY r.id DESC";

$stmt = $conn->prepare($select);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$reclamaciones = array_map("normalizarReclamo", db_fetch_all($stmt->get_result()));

$totales = ["total" => 0, "pendiente" => 0, "en_proceso" => 0, "atendida" => 0];
$resultTotales = $conn->query("SELECT estado, COUNT(*) AS total FROM reclamaciones GROUP BY estado");
if ($resultTotales) {
    while ($row = $resultTotales->fetch_assoc()) {
        $estado = $row["estado"] ?: "pendiente";
        if (isset($totales[$estado])) {
            $totales[$estado] = (int) $row["total"];
        }
        $totales["total"] += (int) $row["total"];
    }
}

$tabs = [
    "total" => "Total",
    "pendiente" => "Pendientes",
    "en_proceso" => "En Proceso",
    "atendida" => "Atendidas",
];

include("layout/header.php");
include("layout/sidebar.php");
?>

<section class="admin-page-heading">
    <h1>Libro de Reclamaciones</h1>
    <p>Gestión de quejas y reclamos de huéspedes</p>
</section>

<?php if ($mensaje): ?>
    <div class="admin-alert admin-alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="admin-alert admin-alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="admin-small-stats">
    <article><span>Total</span><strong><?php echo (int) $totales["total"]; ?></strong></article>
    <article><span>Pendientes</span><strong><?php echo (int) $totales["pendiente"]; ?></strong></article>
    <article><span>En Proceso</span><strong><?php echo (int) $totales["en_proceso"]; ?></strong></article>
    <article><span>Atendidas</span><strong><?php echo (int) $totales["atendida"]; ?></strong></article>
</div>

<nav class="admin-tabs" aria-label="Filtro de reclamaciones">
    <?php foreach ($tabs as $estado => $label): ?>
        <a class="<?php echo $filtro === $estado ? "active" : ""; ?>" href="reclamaciones.php?estado=<?php echo urlencode($estado); ?>">
            <?php echo htmlspecialchars($label); ?>
        </a>
    <?php endforeach; ?>
</nav>

<div class="admin-claim-list">
    <?php if (!$reclamaciones): ?>
        <article>
            <h2>No hay reclamos en esta categoría</h2>
            <p>Cuando cambie el estado de un reclamo, aparecerá automáticamente en el filtro correspondiente.</p>
        </article>
    <?php endif; ?>

    <?php foreach ($reclamaciones as $reclamo): ?>
        <article id="<?php echo htmlspecialchars($reclamo["id"]); ?>">
            <h2>
                <i class="bi bi-exclamation-triangle"></i>
                <?php echo htmlspecialchars($reclamo["titulo"]); ?>
                <span class="claim-status claim-status-<?php echo htmlspecialchars($reclamo["estado"]); ?>"><?php echo estadoTexto($reclamo["estado"]); ?></span>
            </h2>
            <small>ID: <?php echo htmlspecialchars($reclamo["id"]); ?> · Fecha: <?php echo htmlspecialchars($reclamo["fecha"]); ?> · Prioridad <?php echo htmlspecialchars($reclamo["prioridad"]); ?></small>
            <p><b>Huésped:</b><br><?php echo htmlspecialchars($reclamo["huesped"]); ?></p>
            <p><b>Tipo de Reclamo:</b> <em><?php echo htmlspecialchars($reclamo["tipo"]); ?></em></p>
            <p><?php echo htmlspecialchars($reclamo["descripcion"]); ?></p>

            <?php if ($detalleId === $reclamo["id"]): ?>
                <div class="admin-claim-detail">
                    <strong>Detalle del reclamo</strong>
                    <p><?php echo htmlspecialchars($reclamo["detalle"]); ?></p>
                    <a href="reclamaciones.php?estado=<?php echo urlencode($filtro); ?>">Ocultar detalle</a>
                </div>
            <?php endif; ?>

            <div class="admin-claim-actions">
                <a class="admin-claim-button" href="reclamaciones.php?estado=<?php echo urlencode($filtro); ?>&detalle=<?php echo urlencode($reclamo["id"]); ?>#<?php echo urlencode($reclamo["id"]); ?>">Ver Detalle</a>

                <?php if ($reclamo["estado"] !== "en_proceso" && $reclamo["estado"] !== "atendida"): ?>
                    <form method="post" action="reclamaciones.php?estado=<?php echo urlencode($filtro); ?>">
                        <input type="hidden" name="reclamo_id" value="<?php echo (int) $reclamo["id_db"]; ?>">
                        <input type="hidden" name="estado" value="en_proceso">
                        <button type="submit">Marcar En Proceso</button>
                    </form>
                <?php endif; ?>

                <?php if ($reclamo["estado"] !== "atendida"): ?>
                    <form method="post" action="reclamaciones.php?estado=<?php echo urlencode($filtro); ?>">
                        <input type="hidden" name="reclamo_id" value="<?php echo (int) $reclamo["id_db"]; ?>">
                        <input type="hidden" name="estado" value="atendida">
                        <button class="admin-claim-check" type="submit" title="Marcar como atendida"><i class="bi bi-check"></i></button>
                    </form>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>
</div>

<?php include("layout/footer.php"); ?>
