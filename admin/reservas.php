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

$tieneTotal = db_column_exists($conn, "reservas", "total");
$tieneNoches = db_column_exists($conn, "reservas", "noches");
$tieneMetodoPago = db_column_exists($conn, "reservas", "metodo_pago");

$sql = "SELECT
            reservas.*,
            usuarios.nombre AS cliente,
            habitaciones.nombre AS habitacion
        FROM reservas
        INNER JOIN usuarios ON reservas.usuario_id = usuarios.id
        INNER JOIN habitaciones ON reservas.habitacion_id = habitaciones.id
        ORDER BY reservas.id DESC";

$resultado = $conn->query($sql);
?>

<div class="container-fluid">

    <h2 class="mb-4">Administraci&oacute;n de Reservas</h2>

    <div class="card shadow">

        <div class="card-body">

            <div class="table-responsive">

                <table id="tablaReservas" class="table table-striped table-hover align-middle">

                    <thead class="table-dark">

                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Habitaci&oacute;n</th>
                            <th>Ingreso</th>
                            <th>Salida</th>
                            <th>Adultos</th>
                            <th>Ni&ntilde;os</th>
                            <?php if ($tieneNoches) { ?><th>Noches</th><?php } ?>
                            <?php if ($tieneTotal) { ?><th>Total</th><?php } ?>
                            <?php if ($tieneMetodoPago) { ?><th>Pago</th><?php } ?>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php while ($fila = $resultado->fetch_assoc()) { ?>

                    <tr>
                        <td><?php echo (int) $fila['id']; ?></td>
                        <td><?php echo htmlspecialchars($fila['cliente']); ?></td>
                        <td><?php echo htmlspecialchars($fila['habitacion']); ?></td>
                        <td><?php echo htmlspecialchars($fila['fecha_ingreso']); ?></td>
                        <td><?php echo htmlspecialchars($fila['fecha_salida']); ?></td>
                        <td><?php echo (int) $fila['adultos']; ?></td>
                        <td><?php echo (int) $fila['ninos']; ?></td>
                        <?php if ($tieneNoches) { ?><td><?php echo (int) $fila['noches']; ?></td><?php } ?>
                        <?php if ($tieneTotal) { ?><td>S/ <?php echo number_format((float) $fila['total'], 0); ?></td><?php } ?>
                        <?php if ($tieneMetodoPago) { ?><td><?php echo htmlspecialchars(ucfirst($fila['metodo_pago'] ?: "pendiente")); ?></td><?php } ?>

                        <td>
                        <?php
                        if ($fila['estado'] == "pendiente") {
                            echo "<span class='badge bg-warning text-dark'>Pendiente</span>";
                        } elseif ($fila['estado'] == "confirmada") {
                            echo "<span class='badge bg-success'>Confirmada</span>";
                        } else {
                            echo "<span class='badge bg-danger'>Cancelada</span>";
                        }
                        ?>
                        </td>

                        <td>
                            <a href="ver_reserva.php?id=<?php echo (int) $fila['id']; ?>" class="admin-action-btn view" title="Ver detalle">
                                <i class="bi bi-eye-fill"></i>
                            </a>

                            <?php if ($fila['estado'] == "pendiente") { ?>
                                <a href="confirmar_reserva.php?id=<?php echo (int) $fila['id']; ?>" class="admin-action-btn confirm js-reserva-confirm" title="Confirmar reserva">
                                    <i class="bi bi-check-circle-fill"></i>
                                </a>

                                <a href="cancelar_reserva.php?id=<?php echo (int) $fila['id']; ?>" class="admin-action-btn cancel js-reserva-cancel" title="Cancelar reserva">
                                    <i class="bi bi-x-circle-fill"></i>
                                </a>
                            <?php } ?>
                        </td>
                    </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php include("layout/footer.php"); ?>
