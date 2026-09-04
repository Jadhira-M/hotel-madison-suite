<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");
include("../config/db_utils.php");

function estrellas($cantidad)
{
    return str_repeat("*", max(0, (int) $cantidad));
}

$usaTablas = db_table_exists($conn, "servicios_hotel") && db_table_exists($conn, "proveedores") && db_table_exists($conn, "proveedor_historial");
$historialId = (int) ($_GET["historial"] ?? 0);
$servicios = [];
$proveedores = [];
$proveedorSeleccionado = null;
$historial = [];

if ($usaTablas) {
    $servicios = db_fetch_all($conn->query("SELECT * FROM servicios_hotel ORDER BY id ASC"));
    $proveedores = db_fetch_all($conn->query("SELECT
            p.*,
            COUNT(ph.id) AS total_historial,
            SUM(CASE WHEN ph.estado = 'completado' THEN 1 ELSE 0 END) AS total_completado
        FROM proveedores p
        LEFT JOIN proveedor_historial ph ON ph.proveedor_id = p.id
        GROUP BY p.id
        ORDER BY p.categoria ASC"));

    if ($historialId > 0) {
        $stmtProveedor = $conn->prepare("SELECT * FROM proveedores WHERE id = ?");
        $stmtProveedor->bind_param("i", $historialId);
        $stmtProveedor->execute();
        $proveedorSeleccionado = $stmtProveedor->get_result()->fetch_assoc();

        if ($proveedorSeleccionado) {
            $stmtHistorial = $conn->prepare("SELECT * FROM proveedor_historial WHERE proveedor_id = ? ORDER BY fecha DESC, id DESC");
            $stmtHistorial->bind_param("i", $historialId);
            $stmtHistorial->execute();
            $historial = db_fetch_all($stmtHistorial->get_result());
        }
    }
}

include("layout/header.php");
include("layout/sidebar.php");
?>

<section class="admin-page-heading">
    <h1>Inventario y Servicios</h1>
    <p>Control de servicios activos, amenidades y proveedores del hotel</p>
</section>

<?php if (!$usaTablas): ?>
    <div class="admin-alert admin-alert-error">Primero ejecuta el SQL de mejoras dinamicas para activar inventario y proveedores en MySQL.</div>
<?php endif; ?>

<h2 class="admin-section-title">Estado de Servicios</h2>

<div class="admin-service-grid">
    <?php foreach ($servicios as $servicio): ?>
        <article>
            <i class="bi bi-<?php echo htmlspecialchars($servicio["icono"] ?: "stars"); ?>"></i>
            <span><?php echo htmlspecialchars(ucfirst($servicio["estado"])); ?></span>
            <h3><?php echo htmlspecialchars($servicio["nombre"]); ?></h3>
            <p><?php echo htmlspecialchars($servicio["descripcion"]); ?></p>
            <small><?php echo htmlspecialchars($servicio["categoria"]); ?></small>
        </article>
    <?php endforeach; ?>
</div>

<h2 class="admin-section-title mt-5">Proveedores y Mantenimientos</h2>

<div class="admin-provider-grid">
    <?php foreach ($proveedores as $proveedor): ?>
        <article class="<?php echo $historialId === (int) $proveedor["id"] ? "active" : ""; ?>">
            <h3><?php echo htmlspecialchars($proveedor["categoria"]); ?></h3>
            <div class="stars"><?php echo htmlspecialchars(estrellas($proveedor["calificacion"])); ?></div>
            <p><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($proveedor["email"]); ?></p>
            <p><i class="bi bi-phone"></i> <?php echo htmlspecialchars($proveedor["telefono"]); ?></p>
            <p><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($proveedor["direccion"]); ?></p>
            <small>Total Historial</small>
            <strong><?php echo (int) $proveedor["total_completado"]; ?>/<?php echo (int) $proveedor["total_historial"]; ?></strong>
            <a class="admin-provider-button" href="inventario.php?historial=<?php echo (int) $proveedor["id"]; ?>#historial-servicio">Ver Historial</a>
        </article>
    <?php endforeach; ?>
</div>

<?php if ($proveedorSeleccionado): ?>
    <section class="admin-history-panel" id="historial-servicio">
        <div class="admin-history-heading">
            <div>
                <h2>Historial de <?php echo htmlspecialchars($proveedorSeleccionado["categoria"]); ?></h2>
                <p><?php echo htmlspecialchars($proveedorSeleccionado["email"]); ?> - <?php echo htmlspecialchars($proveedorSeleccionado["telefono"]); ?></p>
            </div>
            <a href="inventario.php">Cerrar historial</a>
        </div>

        <div class="admin-history-list">
            <?php if (!$historial): ?>
                <article><p>Este proveedor todavia no tiene historial registrado.</p></article>
            <?php endif; ?>
            <?php foreach ($historial as $item): ?>
                <article>
                    <div>
                        <strong><?php echo htmlspecialchars($item["servicio"]); ?></strong>
                        <span><?php echo htmlspecialchars($item["fecha"]); ?> - <?php echo htmlspecialchars($item["ubicacion"]); ?></span>
                    </div>
                    <span class="admin-history-status"><?php echo htmlspecialchars(ucfirst($item["estado"])); ?></span>
                    <p><?php echo htmlspecialchars($item["observacion"]); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php include("layout/footer.php"); ?>
