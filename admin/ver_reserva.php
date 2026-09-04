<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION['rol'] != "admin") {
    header("Location: ../index.php");
    exit();
}

include("../config/conexion.php");
include("../config/db_utils.php");
include("layout/header.php");
include("layout/sidebar.php");

$id = (int) ($_GET['id'] ?? 0);
$tieneTotal = db_column_exists($conn, "reservas", "total");
$tieneNoches = db_column_exists($conn, "reservas", "noches");
$tieneMetodoPago = db_column_exists($conn, "reservas", "metodo_pago");

$sql = "SELECT
reservas.*,
usuarios.nombre AS cliente,
usuarios.correo,
habitaciones.nombre AS habitacion,
habitaciones.tipo
FROM reservas
INNER JOIN usuarios ON reservas.usuario_id = usuarios.id
INNER JOIN habitaciones ON reservas.habitacion_id = habitaciones.id
WHERE reservas.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$reserva = $resultado->fetch_assoc();

if (!$reserva) {
    die("Reserva no encontrada.");
}
?>

<div class="container-fluid">

<div class="card admin-reservation-detail">

<div class="card-header">
<h3>Detalle de la Reserva</h3>
</div>

<div class="card-body">

<table class="table table-bordered align-middle">

<tr>
<th>Cliente</th>
<td><?php echo htmlspecialchars($reserva['cliente']); ?></td>
</tr>

<tr>
<th>Correo</th>
<td><?php echo htmlspecialchars($reserva['correo']); ?></td>
</tr>

<tr>
<th>Habitaci&oacute;n</th>
<td><?php echo htmlspecialchars($reserva['habitacion']); ?></td>
</tr>

<tr>
<th>Tipo</th>
<td><?php echo htmlspecialchars($reserva['tipo']); ?></td>
</tr>

<tr>
<th>Fecha ingreso</th>
<td><?php echo htmlspecialchars($reserva['fecha_ingreso']); ?></td>
</tr>

<tr>
<th>Fecha salida</th>
<td><?php echo htmlspecialchars($reserva['fecha_salida']); ?></td>
</tr>

<tr>
<th>Adultos</th>
<td><?php echo (int) $reserva['adultos']; ?></td>
</tr>

<tr>
<th>Ni&ntilde;os</th>
<td><?php echo (int) $reserva['ninos']; ?></td>
</tr>

<?php if ($tieneNoches) { ?>
<tr>
<th>Noches</th>
<td><?php echo (int) $reserva['noches']; ?></td>
</tr>
<?php } ?>

<?php if ($tieneTotal) { ?>
<tr>
<th>Total</th>
<td>S/ <?php echo number_format((float) $reserva['total'], 0); ?></td>
</tr>
<?php } ?>

<?php if ($tieneMetodoPago) { ?>
<tr>
<th>Pago</th>
<td><?php echo htmlspecialchars(ucfirst($reserva['metodo_pago'] ?: "pendiente")); ?></td>
</tr>
<?php } ?>

<tr>
<th>Estado</th>
<td>
<?php
if ($reserva['estado'] == "pendiente") {
    echo "<span class='badge bg-warning text-dark'>Pendiente</span>";
} elseif ($reserva['estado'] == "confirmada") {
    echo "<span class='badge bg-success'>Confirmada</span>";
} else {
    echo "<span class='badge bg-danger'>Cancelada</span>";
}
?>
</td>
</tr>

</table>

<div class="admin-detail-actions">
<a href="reservas.php" class="btn btn-secondary">Volver</a>

<?php if ($reserva['estado'] == "pendiente") { ?>
<a href="confirmar_reserva.php?id=<?php echo (int) $reserva['id']; ?>" class="btn btn-success js-reserva-confirm">Confirmar</a>
<a href="cancelar_reserva.php?id=<?php echo (int) $reserva['id']; ?>" class="btn btn-danger js-reserva-cancel">Cancelar</a>
<?php } ?>
</div>

</div>

</div>

</div>

<?php include("layout/footer.php"); ?>
