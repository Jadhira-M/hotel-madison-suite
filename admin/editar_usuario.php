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

$id = $_GET['id'];

$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    echo "<div class='container mt-5'>
            <div class='alert alert-danger'>
                Usuario no encontrado.
            </div>
          </div>";
    include("layout/footer.php");
    exit();
}
?>

<div class="container-fluid">

    <div class="card shadow">

        <div class="card-header bg-warning">

            <h3>
                <i class="bi bi-pencil-square"></i>
                Editar Usuario
            </h3>

        </div>

        <div class="card-body">

            <form action="actualizar_usuario.php" method="POST">

                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">

                <div class="mb-3">

                    <label class="form-label">Nombre</label>

                    <input
                    type="text"
                    name="nombre"
                    class="form-control"
                    value="<?php echo $usuario['nombre']; ?>"
                    required>

                </div>

                <div class="mb-3">

                    <label class="form-label">Correo</label>

                    <input
                    type="email"
                    name="correo"
                    class="form-control"
                    value="<?php echo $usuario['correo']; ?>"
                    required>

                </div>

                <div class="mb-3">

                    <label class="form-label">Rol</label>

                    <select name="rol" class="form-select">

                        <option value="cliente"
                        <?php if($usuario['rol']=="cliente") echo "selected"; ?>>
                        Cliente
                        </option>

                        <option value="admin"
                        <?php if($usuario['rol']=="admin") echo "selected"; ?>>
                        Administrador
                        </option>

                    </select>

                </div>

                <button class="btn btn-success">
                    <i class="bi bi-save"></i>
                    Guardar cambios
                </button>

                <a href="usuarios.php" class="btn btn-secondary">
                    Cancelar
                </a>

            </form>

        </div>

    </div>

</div>

<?php include("layout/footer.php"); ?>