<?php
session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION['rol'] != "admin") {
    header("Location: ../index.php");
    exit();
}

include("../config/conexion.php");
include("../config/db_utils.php");

function kpi_soles($monto)
{
    return "S/ " . number_format((float) $monto, 0);
}

$tieneTotal = db_column_exists($conn, "reservas", "total");
$totalExpr = $tieneTotal
    ? "COALESCE(NULLIF(r.total, 0), GREATEST(DATEDIFF(r.fecha_salida, r.fecha_ingreso), 1) * h.precio)"
    : "GREATEST(DATEDIFF(r.fecha_salida, r.fecha_ingreso), 1) * h.precio";

$totalUsuarios = (int) $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];
$totalHabitaciones = (int) $conn->query("SELECT COUNT(*) AS total FROM habitaciones")->fetch_assoc()['total'];
$habitacionesOcupadas = (int) $conn->query("SELECT COUNT(*) AS total FROM habitaciones WHERE estado = 'ocupada'")->fetch_assoc()['total'];
$totalReservas = (int) $conn->query("SELECT COUNT(*) AS total FROM reservas")->fetch_assoc()['total'];
$checkinsHoy = (int) $conn->query("SELECT COUNT(*) AS total FROM reservas WHERE fecha_ingreso = CURDATE() AND estado <> 'cancelada'")->fetch_assoc()['total'];
$pendientes = (int) $conn->query("SELECT COUNT(*) AS total FROM reservas WHERE estado = 'pendiente'")->fetch_assoc()['total'];
$ingresosHoy = (float) $conn->query("SELECT COALESCE(SUM({$totalExpr}), 0) AS total FROM reservas r INNER JOIN habitaciones h ON h.id = r.habitacion_id WHERE r.fecha_ingreso = CURDATE() AND r.estado <> 'cancelada'")->fetch_assoc()['total'];
$ingresosMes = (float) $conn->query("SELECT COALESCE(SUM({$totalExpr}), 0) AS total FROM reservas r INNER JOIN habitaciones h ON h.id = r.habitacion_id WHERE MONTH(r.fecha_ingreso) = MONTH(CURDATE()) AND YEAR(r.fecha_ingreso) = YEAR(CURDATE()) AND r.estado <> 'cancelada'")->fetch_assoc()['total'];
$reservasHoy = (int) $conn->query("SELECT COUNT(*) AS total FROM reservas WHERE DATE(fecha_ingreso) = CURDATE()")->fetch_assoc()['total'];
$ocupacionActual = $totalHabitaciones > 0 ? round(($habitacionesOcupadas / $totalHabitaciones) * 100) : 0;

$notificacionesUrgentes = $pendientes;
if (db_table_exists($conn, "reclamaciones")) {
    $notificacionesUrgentes += (int) $conn->query("SELECT COUNT(*) AS total FROM reclamaciones WHERE estado IN ('pendiente','en_proceso')")->fetch_assoc()['total'];
}

$sql = "SELECT
usuarios.nombre,
habitaciones.nombre AS habitacion,
reservas.fecha_ingreso,
reservas.estado
FROM reservas
INNER JOIN usuarios ON reservas.usuario_id = usuarios.id
INNER JOIN habitaciones ON reservas.habitacion_id = habitaciones.id
WHERE reservas.estado <> 'cancelada'
ORDER BY reservas.fecha_ingreso ASC, reservas.id DESC
LIMIT 5";

$resultado = $conn->query($sql);

$reclamos = [];
if (db_table_exists($conn, "reclamaciones")) {
    $reclamos = db_fetch_all($conn->query("SELECT tipo, detalle, estado FROM reclamaciones WHERE estado IN ('pendiente','en_proceso') ORDER BY id DESC LIMIT 3"));
}

include("layout/header.php");
include("layout/sidebar.php");
?>

<section class="admin-page-heading">
<h1>Dashboard</h1>
<p>Vista general del estado del hotel y ultimas reservas.</p>
</section>

<section class="admin-kpi-grid">

<article class="admin-kpi-card">
<span><i class="bi bi-door-open-fill"></i></span>
<p>Habitaciones ocupadas</p>
<strong><?php echo $habitacionesOcupadas; ?> / <?php echo $totalHabitaciones; ?></strong>
<small><?php echo $ocupacionActual; ?>% de ocupacion actual</small>
</article>

<article class="admin-kpi-card">
<span><i class="bi bi-cash-coin"></i></span>
<p>Ingresos del dia</p>
<strong><?php echo kpi_soles($ingresosHoy); ?></strong>
<small><?php echo kpi_soles($ingresosMes); ?> este mes</small>
</article>

<article class="admin-kpi-card">
<span><i class="bi bi-calendar-check-fill"></i></span>
<p>Check-ins hoy</p>
<strong><?php echo $checkinsHoy; ?></strong>
<small><?php echo $pendientes; ?> pendientes</small>
</article>

<article class="admin-kpi-card">
<span><i class="bi bi-exclamation-circle-fill"></i></span>
<p>Notificaciones urgentes</p>
<strong><?php echo $notificacionesUrgentes; ?></strong>
<small>Requieren atencion</small>
</article>

</section>

<section class="admin-dashboard-grid">

<article class="admin-panel">

<h2>Proximos Check-ins</h2>

<?php if ($resultado && $resultado->num_rows > 0) { ?>

<div class="admin-mini-list">

<?php while ($fila = $resultado->fetch_assoc()) { ?>

<div>
<strong><?php echo htmlspecialchars($fila['nombre']); ?></strong>
<span><?php echo htmlspecialchars($fila['habitacion']); ?></span>
<small><?php echo htmlspecialchars($fila['fecha_ingreso']); ?></small>
</div>

<?php } ?>

</div>

<?php } else { ?>

<p class="text-muted mb-0">Aun no hay reservas registradas.</p>

<?php } ?>

</article>

<article class="admin-panel">

<h2>Notificaciones Urgentes</h2>

<div class="admin-alert-list">
<?php if ($reclamos): ?>
    <?php foreach ($reclamos as $reclamo): ?>
        <div>
        <strong><?php echo htmlspecialchars(ucfirst($reclamo["tipo"] ?? "Reclamo")); ?></strong>
        <span><?php echo htmlspecialchars($reclamo["detalle"] ?? "Reclamo registrado"); ?></span>
        <small><?php echo htmlspecialchars(ucfirst($reclamo["estado"])); ?></small>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div>
    <strong>Reservas</strong>
    <span><?php echo $pendientes; ?> reservas pendientes de revision</span>
    <small>Estado actual</small>
    </div>
<?php endif; ?>
</div>

</article>

</section>

<section class="admin-summary-panel">

<h2>Resumen Mensual</h2>

<div class="admin-summary-board">

<div>
<span>Ocupacion actual</span>
<strong><?php echo $ocupacionActual; ?>%</strong>
<small>(<?php echo $habitacionesOcupadas; ?>/<?php echo $totalHabitaciones; ?>)</small>
</div>

<div>
<span>Reservas hoy</span>
<strong><?php echo $reservasHoy; ?></strong>
<small><?php echo kpi_soles($ingresosHoy); ?> ingreso estimado</small>
</div>

<div>
<span>Usuarios</span>
<strong><?php echo $totalUsuarios; ?></strong>
<small>registrados</small>
</div>

<div>
<span>Reservas</span>
<strong><?php echo $totalReservas; ?></strong>
<small>totales</small>
</div>

</div>

</section>

<?php include("layout/footer.php"); ?>
