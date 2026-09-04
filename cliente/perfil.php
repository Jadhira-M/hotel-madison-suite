<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");

$id = (int) $_SESSION['id_usuario'];
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

include("../includes/header.php");
?>

<body>
<?php include("../includes/navbar.php"); ?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body p-4">
                    <p class="section-kicker">Mi cuenta</p>
                    <h1 class="fw-bold mb-4">Mi Perfil</h1>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <tr>
                                <th>Nombre</th>
                                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                            </tr>
                            <tr>
                                <th>Correo</th>
                                <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                            </tr>
                            <tr>
                                <th>Rol</th>
                                <td><?php echo htmlspecialchars(ucfirst($usuario['rol'])); ?></td>
                            </tr>
                            <?php if (isset($usuario["telefono"])): ?>
                                <tr>
                                    <th>Tel&eacute;fono</th>
                                    <td><?php echo htmlspecialchars($usuario["telefono"] ?: "No registrado"); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (isset($usuario["ciudad"])): ?>
                                <tr>
                                    <th>Ciudad</th>
                                    <td><?php echo htmlspecialchars($usuario["ciudad"] ?: "No registrada"); ?></td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>

                    <div class="profile-actions d-flex flex-wrap gap-2">
                        <a href="dashboard.php" class="btn btn-outline-secondary">Volver</a>
                        <a href="cambiar_password.php" class="btn btn-warning">Cambiar contrase&ntilde;a</a>
                        <a href="historial.php" class="btn btn-outline-warning">Ver historial</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include("../includes/footer.php"); ?>
</body>
</html>
