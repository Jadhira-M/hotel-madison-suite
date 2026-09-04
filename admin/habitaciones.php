<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

include("../config/conexion.php");
include("layout/header.php");
include("layout/sidebar.php");

// Obtener habitaciones
$sql = "SELECT * FROM habitaciones ORDER BY id DESC";
$resultado = $conn->query($sql);
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Gestión de Habitaciones</h2>

        <a href="agregar_habitacion.php" class="btn btn-success">
            <i class="bi bi-plus-circle-fill"></i> Nueva Habitación
        </a>

    </div>

    <div class="card shadow">

        <div class="card-body">

            <div class="table-responsive">

                <table id="tablaHabitaciones" class="table table-striped table-hover align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th>ID</th>

                            <th>Imagen</th>

                            <th>Número</th>

                            <th>Nombre</th>

                            <th>Tipo</th>

                            <th>Precio</th>

                            <th>Capacidad</th>

                            <th>Estado</th>

                            <th>Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php while($fila = $resultado->fetch_assoc()){ ?>

                        <tr>

                            <td><?php echo $fila['id']; ?></td>

                            <td>

                                <?php if(!empty($fila['imagen'])){ ?>

                                    <img
                                    src="../assets/img/habitaciones/<?php echo $fila['imagen']; ?>"
                                    width="90"
                                    class="rounded">

                                <?php }else{ ?>

                                    Sin imagen

                                <?php } ?>

                            </td>

                            <td><?php echo $fila['numero']; ?></td>

                            <td><?php echo $fila['nombre']; ?></td>

                            <td><?php echo $fila['tipo']; ?></td>

                            <td>S/. <?php echo number_format($fila['precio'],2); ?></td>

                            <td><?php echo $fila['capacidad']; ?></td>

                            <td>

                                <?php
                                if($fila['estado']=="disponible"){
                                    echo "<span class='badge bg-success'>Disponible</span>";
                                }else{
                                    echo "<span class='badge bg-danger'>Ocupada</span>";
                                }
                                ?>

                            </td>

                            <td>

                                <a
                                href="editar_habitacion.php?id=<?php echo $fila['id']; ?>"
                                class="btn btn-warning btn-sm">

                                <i class="bi bi-pencil-square"></i>

                                </a>

                                <a
                                href="habitacion_imagenes.php?id=<?php echo $fila['id']; ?>"
                                class="btn btn-info btn-sm"
                                title="Administrar imágenes">

                                <i class="bi bi-images"></i>

                                </a>

                                <a
                                href="eliminar_habitacion.php?id=<?php echo $fila['id']; ?>"
                                class="btn btn-danger btn-sm btn-eliminar">

                                <i class="bi bi-trash-fill"></i>

                                </a>

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
