<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");
include("../config/db_utils.php");

function soles($monto)
{
    return "S/ " . number_format((float) $monto, 0);
}

function estadoReserva($estado)
{
    return ucfirst(strtolower((string) $estado));
}

$query = trim($_GET["q"] ?? "");
$historialId = (int) ($_GET["historial"] ?? 0);
$tieneTotal = db_column_exists($conn, "reservas", "total");
$tieneNoches = db_column_exists($conn, "reservas", "noches");
$usuariosTieneTelefono = db_column_exists($conn, "usuarios", "telefono");
$usuariosTieneCiudad = db_column_exists($conn, "usuarios", "ciudad");

$telefonoSelect = $usuariosTieneTelefono ? "u.telefono" : "'' AS telefono";
$ciudadSelect = $usuariosTieneCiudad ? "u.ciudad" : "'' AS ciudad";
$totalExpr = $tieneTotal
    ? "COALESCE(NULLIF(r.total, 0), GREATEST(DATEDIFF(r.fecha_salida, r.fecha_ingreso), 1) * h.precio)"
    : "GREATEST(DATEDIFF(r.fecha_salida, r.fecha_ingreso), 1) * h.precio";
$nochesExpr = $tieneNoches
    ? "COALESCE(NULLIF(r.noches, 0), GREATEST(DATEDIFF(r.fecha_salida, r.fecha_ingreso), 1))"
    : "GREATEST(DATEDIFF(r.fecha_salida, r.fecha_ingreso), 1)";

$where = "WHERE u.rol = 'cliente'";
$params = [];
$types = "";

if ($query !== "") {
    $where .= " AND (u.nombre LIKE ? OR u.correo LIKE ?";
    $params[] = "%{$query}%";
    $params[] = "%{$query}%";
    $types .= "ss";

    if ($usuariosTieneTelefono) {
        $where .= " OR u.telefono LIKE ?";
        $params[] = "%{$query}%";
        $types .= "s";
    }

    if ($usuariosTieneCiudad) {
        $where .= " OR u.ciudad LIKE ?";
        $params[] = "%{$query}%";
        $types .= "s";
    }

    $where .= ")";
}

$sqlClientes = "SELECT
        u.id,
        u.nombre,
        u.correo,
        {$telefonoSelect},
        {$ciudadSelect},
        COUNT(r.id) AS total_estadias,
        COALESCE(SUM(CASE WHEN r.estado <> 'cancelada' THEN {$totalExpr} ELSE 0 END), 0) AS total_gastado,
        MAX(r.fecha_ingreso) AS ultima_visita
    FROM usuarios u
    LEFT JOIN reservas r ON r.usuario_id = u.id
    LEFT JOIN habitaciones h ON h.id = r.habitacion_id
    {$where}
    GROUP BY u.id, u.nombre, u.correo" . ($usuariosTieneTelefono ? ", u.telefono" : "") . ($usuariosTieneCiudad ? ", u.ciudad" : "") . "
    ORDER BY total_gastado DESC, u.nombre ASC";

$stmtClientes = $conn->prepare($sqlClientes);
if ($types !== "") {
    $stmtClientes->bind_param($types, ...$params);
}
$stmtClientes->execute();
$clientesFiltrados = db_fetch_all($stmtClientes->get_result());

$sqlMetricas = "SELECT
        COUNT(*) AS total_clientes,
        COALESCE(SUM(datos.total_gastado), 0) AS ingresos_totales,
        COALESCE(AVG(datos.total_estadias), 0) AS promedio_estadias,
        SUM(CASE WHEN datos.total_gastado >= 2000 OR datos.total_estadias >= 5 THEN 1 ELSE 0 END) AS clientes_vip
    FROM (
        SELECT
            u.id,
            COUNT(r.id) AS total_estadias,
            COALESCE(SUM(CASE WHEN r.estado <> 'cancelada' THEN {$totalExpr} ELSE 0 END), 0) AS total_gastado
        FROM usuarios u
        LEFT JOIN reservas r ON r.usuario_id = u.id
        LEFT JOIN habitaciones h ON h.id = r.habitacion_id
        WHERE u.rol = 'cliente'
        GROUP BY u.id
    ) datos";
$metricas = $conn->query($sqlMetricas)->fetch_assoc();

$topClientes = db_fetch_all($conn->query("SELECT
        u.id,
        u.nombre,
        COUNT(r.id) AS total_estadias,
        COALESCE(SUM(CASE WHEN r.estado <> 'cancelada' THEN {$totalExpr} ELSE 0 END), 0) AS total_gastado
    FROM usuarios u
    LEFT JOIN reservas r ON r.usuario_id = u.id
    LEFT JOIN habitaciones h ON h.id = r.habitacion_id
    WHERE u.rol = 'cliente'
    GROUP BY u.id, u.nombre
    ORDER BY total_gastado DESC
    LIMIT 5"));

$clienteSeleccionado = null;
$historial = [];

if ($historialId > 0) {
    $stmtCliente = $conn->prepare("SELECT id, nombre, correo, {$telefonoSelect}, {$ciudadSelect} FROM usuarios u WHERE u.id = ? AND u.rol = 'cliente'");
    $stmtCliente->bind_param("i", $historialId);
    $stmtCliente->execute();
    $clienteSeleccionado = $stmtCliente->get_result()->fetch_assoc();

    if ($clienteSeleccionado) {
        $sqlHistorial = "SELECT
                r.fecha_ingreso,
                r.fecha_salida,
                r.estado,
                h.nombre AS habitacion,
                h.precio AS precio_noche,
                {$nochesExpr} AS noches,
                {$totalExpr} AS subtotal
            FROM reservas r
            INNER JOIN habitaciones h ON h.id = r.habitacion_id
            WHERE r.usuario_id = ?
            ORDER BY r.fecha_ingreso DESC, r.id DESC";
        $stmtHistorial = $conn->prepare($sqlHistorial);
        $stmtHistorial->bind_param("i", $historialId);
        $stmtHistorial->execute();
        $historial = db_fetch_all($stmtHistorial->get_result());
    }
}

$totalClientes = (int) ($metricas["total_clientes"] ?? 0);
$clientesVip = (int) ($metricas["clientes_vip"] ?? 0);
$ingresosTotales = (float) ($metricas["ingresos_totales"] ?? 0);
$promedioEstadias = round((float) ($metricas["promedio_estadias"] ?? 0), 1);

include("layout/header.php");
include("layout/sidebar.php");
?>

<section class="admin-page-heading">
    <h1>Base de Datos de Clientes</h1>
    <p>Vista de clientes, historial de reservas e ingresos acumulados</p>
</section>

<div class="admin-small-stats client-stats">
    <article><span>Total</span><strong><?php echo $totalClientes; ?></strong></article>
    <article><span>Clientes VIP</span><strong><?php echo $clientesVip; ?></strong></article>
    <article><span>Ingresos Totales</span><strong><?php echo soles($ingresosTotales); ?></strong></article>
    <article><span>Promedio Estadias</span><strong><?php echo htmlspecialchars($promedioEstadias); ?></strong></article>
</div>

<form class="admin-search-box" method="get" action="clientes.php">
    <i class="bi bi-search"></i>
    <input type="text" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Buscar cliente por nombre, email o ciudad...">
    <button type="submit">Buscar</button>
    <?php if ($query !== ""): ?>
        <a href="clientes.php">Limpiar</a>
    <?php endif; ?>
</form>

<h2 class="admin-section-title">Mostrando <?php echo count($clientesFiltrados); ?> de <?php echo $totalClientes; ?> clientes</h2>

<div class="admin-client-cards">
    <?php if (!$clientesFiltrados): ?>
        <article>
            <h3>No se encontraron clientes</h3>
            <p>Prueba con otro nombre, email, telefono o ciudad.</p>
        </article>
    <?php endif; ?>

    <?php foreach ($clientesFiltrados as $cliente): ?>
        <?php $esVip = (float) $cliente["total_gastado"] >= 2000 || (int) $cliente["total_estadias"] >= 5; ?>
        <article class="<?php echo $historialId === (int) $cliente["id"] ? "active" : ""; ?>">
            <h3>
                <?php echo htmlspecialchars($cliente["nombre"]); ?>
                <?php if ($esVip): ?><span>VIP</span><?php endif; ?>
            </h3>
            <p><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($cliente["correo"]); ?></p>
            <p><i class="bi bi-phone"></i> <?php echo htmlspecialchars($cliente["telefono"] ?: "Sin telefono"); ?></p>
            <p><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($cliente["ciudad"] ?: "Sin ciudad"); ?></p>
            <div class="client-card-metrics">
                <div><small>Total Estadias</small><b><?php echo (int) $cliente["total_estadias"]; ?></b></div>
                <div><small>Total Gastado</small><b><?php echo soles($cliente["total_gastado"]); ?></b></div>
            </div>
            <a class="admin-client-button" href="clientes.php?q=<?php echo urlencode($query); ?>&historial=<?php echo (int) $cliente["id"]; ?>#historial-cliente">Ver Historial</a>
        </article>
    <?php endforeach; ?>
</div>

<?php if ($clienteSeleccionado): ?>
    <section class="admin-history-panel" id="historial-cliente">
        <div class="admin-history-heading">
            <div>
                <h2>Historial de <?php echo htmlspecialchars($clienteSeleccionado["nombre"]); ?></h2>
                <p><?php echo htmlspecialchars($clienteSeleccionado["correo"]); ?> - <?php echo htmlspecialchars($clienteSeleccionado["telefono"] ?: "Sin telefono"); ?></p>
            </div>
            <a href="clientes.php<?php echo $query !== "" ? "?q=" . urlencode($query) : ""; ?>">Cerrar historial</a>
        </div>

        <div class="admin-history-list client-history-list">
            <?php if (!$historial): ?>
                <article><p>Este cliente todavia no tiene reservas registradas.</p></article>
            <?php endif; ?>
            <?php foreach ($historial as $reserva): ?>
                <article>
                    <div>
                        <strong><?php echo htmlspecialchars($reserva["habitacion"]); ?></strong>
                        <span><?php echo htmlspecialchars($reserva["fecha_ingreso"]); ?> - <?php echo htmlspecialchars($reserva["fecha_salida"]); ?> - <?php echo (int) $reserva["noches"]; ?> noches</span>
                    </div>
                    <span class="admin-history-status"><?php echo htmlspecialchars(estadoReserva($reserva["estado"])); ?></span>
                    <p>Precio por noche: <b><?php echo soles($reserva["precio_noche"]); ?></b> - Subtotal: <b><?php echo soles($reserva["subtotal"]); ?></b></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<h2 class="admin-section-title mt-5">Top 5 Clientes por Ingresos</h2>

<div class="top-client-list">
    <?php foreach ($topClientes as $i => $cliente): ?>
        <?php $esVip = (float) $cliente["total_gastado"] >= 2000 || (int) $cliente["total_estadias"] >= 5; ?>
        <article>
            <span><?php echo $i + 1; ?></span>
            <div>
                <strong><?php echo htmlspecialchars($cliente["nombre"]); ?></strong>
                <small><?php echo (int) $cliente["total_estadias"]; ?> estadias</small>
            </div>
            <b><?php echo soles($cliente["total_gastado"]); ?> <?php if ($esVip): ?><em>VIP</em><?php endif; ?></b>
        </article>
    <?php endforeach; ?>
</div>

<?php include("layout/footer.php"); ?>
