<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");
include("../includes/header.php");

$usuario_id = $_SESSION['id_usuario'];

function reservaImagenPath($imagen)
{
    $imagen = trim((string) $imagen);
    $fallback = "../assets/img/habitaciones/doble.jpg";

    if ($imagen === "") {
        return $fallback;
    }

    $relative = "../assets/img/habitaciones/" . $imagen;
    $absolute = __DIR__ . "/../assets/img/habitaciones/" . $imagen;

    return is_file($absolute) ? $relative : $fallback;
}

$sql = "SELECT
            reservas.*,
            habitaciones.nombre,
            habitaciones.imagen,
            habitaciones.precio
        FROM reservas
        INNER JOIN habitaciones
            ON reservas.habitacion_id = habitaciones.id
        WHERE reservas.usuario_id = ?
        ORDER BY reservas.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();

$resultado = $stmt->get_result();
?>

<body>

<?php include("../includes/navbar.php"); ?>

<div class="container py-5">

    <h2 class="mb-4">Mis Reservas</h2>

    <?php if($resultado->num_rows > 0){ ?>

    <div class="table-responsive">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark">
                <tr>
                    <th>Habitación</th>
                    <th>Imagen</th>
                    <th>Ingreso</th>
                    <th>Salida</th>
                    <th>Adultos</th>
                    <th>Niños</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>

            <tbody>
            <?php while($fila = $resultado->fetch_assoc()){ ?>

                <tr>
                    <td><?php echo htmlspecialchars($fila['nombre']); ?></td>

                    <td>
                        <img
                        src="<?php echo htmlspecialchars(reservaImagenPath($fila['imagen'])); ?>"
                        alt="<?php echo htmlspecialchars($fila['nombre']); ?>"
                        width="120">
                    </td>

                    <td><?php echo htmlspecialchars($fila['fecha_ingreso']); ?></td>
                    <td><?php echo htmlspecialchars($fila['fecha_salida']); ?></td>
                    <td><?php echo (int) $fila['adultos']; ?></td>
                    <td><?php echo (int) $fila['ninos']; ?></td>

                    <td>
                        <?php
                        if($fila['estado']=="pendiente"){
                            echo "<span class='badge bg-warning text-dark'>Pendiente</span>";
                        }elseif($fila['estado']=="confirmada"){
                            echo "<span class='badge bg-success'>Confirmada</span>";
                        }else{
                            echo "<span class='badge bg-danger'>Cancelada</span>";
                        }
                        ?>
                    </td>

                    <td>
                        <?php if($fila['estado']=="pendiente"){ ?>
                        <a
                        href="cancelar_reserva.php?id=<?php echo (int) $fila['id'];?>"
                        class="btn btn-danger btn-sm">
                        Cancelar
                        </a>
                        <?php } ?>
                    </td>
                </tr>

            <?php } ?>
            </tbody>

        </table>

    </div>

    <?php }else{ ?>

        <div class="alert alert-info">
            Aún no tienes reservas.
        </div>

    <?php } ?>

</div>

<?php include("../includes/footer.php"); ?>

</body>

</html>
