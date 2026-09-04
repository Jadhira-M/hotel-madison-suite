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
include("layout/header.php");
include("layout/sidebar.php");

// Obtener todos los usuarios
$sql = "SELECT * FROM usuarios ORDER BY id DESC";
$resultado = $conn->query($sql);
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>
            <i class="bi bi-people-fill"></i>
            Gestión de Usuarios
        </h2>

    </div>

    <div class="card shadow">

        <div class="card-body">

            <div class="table-responsive">

                <table id="tablaUsuarios" class="table table-striped table-hover align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php while($fila = $resultado->fetch_assoc()){ ?>

                    <tr>

                        <td><?php echo $fila['id']; ?></td>

                        <td><?php echo $fila['nombre']; ?></td>

                        <td><?php echo $fila['correo']; ?></td>

                        <td>

                            <?php
                            if($fila['rol']=="admin"){
                                echo "<span class='badge bg-danger'>Administrador</span>";
                            }else{
                                echo "<span class='badge bg-primary'>Cliente</span>";
                            }
                            ?>

                        </td>

                        <td><?php echo $fila['fecha_registro']; ?></td>

                        <td>

                            <a
                            href="editar_usuario.php?id=<?php echo $fila['id']; ?>"
                            class="btn btn-warning btn-sm">

                            <i class="bi bi-pencil-square"></i>

                            </a>

                            <?php if($fila['id'] != $_SESSION['id_usuario']){ ?>

                            <a
                            href="eliminar_usuario.php?id=<?php echo $fila['id']; ?>"
                            class="btn btn-danger btn-sm btn-eliminar">

                            <i class="bi bi-trash-fill"></i>

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

<?php
$mensaje = $_GET['mensaje'] ?? null;
?>

<?php include("layout/footer.php"); ?>

<?php if($mensaje == "actualizado"){ ?>
<script>
Swal.fire({
    icon: "success",
    title: "¡Éxito!",
    text: "Usuario actualizado correctamente.",
    timer: 2000,
    showConfirmButton: false
});
</script>
<?php } ?>

<?php if($mensaje == "error"){ ?>
<script>
Swal.fire({
    icon: "error",
    title: "Error",
    text: "No se pudo actualizar el usuario."
});
</script>
<?php } ?>

<?php if(isset($_GET['mensaje']) && $_GET['mensaje']=="eliminado"){ ?>
<script>
Swal.fire({
    icon: "success",
    title: "¡Eliminado!",
    text: "Usuario eliminado correctamente.",
    timer: 2000,
    showConfirmButton: false
});
</script>
<?php } ?>

<?php if(isset($_GET['mensaje']) && $_GET['mensaje']=="no_eliminar"){ ?>
<script>
Swal.fire({
    icon: "warning",
    title: "Acción no permitida",
    text: "No puedes eliminar tu propia cuenta."
});
</script>
<?php } ?>

<?php if(isset($_GET['mensaje']) && $_GET['mensaje']=="error_eliminar"){ ?>
<script>
Swal.fire({
    icon: "error",
    title: "Error",
    text: "No se pudo eliminar el usuario."
});
</script>
<?php } ?>