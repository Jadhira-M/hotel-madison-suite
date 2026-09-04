<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");

$mensaje = "";
$error = "";
$usuarioId = (int) $_SESSION["id_usuario"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $actual = $_POST["actual"] ?? "";
    $nueva = $_POST["nueva"] ?? "";
    $confirmar = $_POST["confirmar"] ?? "";

    if ($actual === "" || $nueva === "" || $confirmar === "") {
        $error = "Completa todos los campos.";
    } elseif ($nueva !== $confirmar) {
        $error = "La nueva contraseña no coincide.";
    } elseif (strlen($nueva) < 6) {
        $error = "La nueva contraseña debe tener al menos 6 caracteres.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();

        if (!$usuario || !password_verify($actual, $usuario["password"])) {
            $error = "La contraseña actual no es correcta.";
        } else {
            $hash = password_hash($nueva, PASSWORD_DEFAULT);
            $stmtUpdate = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmtUpdate->bind_param("si", $hash, $usuarioId);
            $mensaje = $stmtUpdate->execute() ? "Contraseña actualizada correctamente." : "";
            $error = $mensaje ? "" : "No se pudo actualizar la contraseña.";
        }
    }
}

include("../includes/header.php");
?>

<body>
<?php include("../includes/navbar.php"); ?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <p class="section-kicker">Mi cuenta</p>
                    <h1 class="fw-bold mb-3">Cambiar Contrase&ntilde;a</h1>

                    <?php if ($mensaje): ?><div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="alert alert-warning"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Contrase&ntilde;a actual</label>
                            <input type="password" name="actual" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Nueva contrase&ntilde;a</label>
                            <input type="password" name="nueva" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label>Confirmar nueva contrase&ntilde;a</label>
                            <input type="password" name="confirmar" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Guardar cambio</button>
                        <a href="dashboard.php" class="btn btn-outline-secondary">Volver</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include("../includes/footer.php"); ?>
</body>
</html>
