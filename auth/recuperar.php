<?php
session_start();
include("../config/conexion.php");

$mensaje = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = trim($_POST["correo"] ?? "");
    $nuevaPassword = $_POST["password"] ?? "";
    $confirmar = $_POST["confirmar"] ?? "";

    if ($correo === "" || $nuevaPassword === "" || $confirmar === "") {
        $error = "Completa todos los campos.";
    } elseif ($nuevaPassword !== $confirmar) {
        $error = "Las contraseñas no coinciden.";
    } elseif (strlen($nuevaPassword) < 6) {
        $error = "La nueva contraseña debe tener al menos 6 caracteres.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();

        if (!$usuario) {
            $error = "No encontramos una cuenta con ese correo.";
        } else {
            $hash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
            $stmtUpdate = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmtUpdate->bind_param("si", $hash, $usuario["id"]);

            if ($stmtUpdate->execute()) {
                $mensaje = "Tu contraseña fue actualizada. Ahora puedes iniciar sesión.";
            } else {
                $error = "No se pudo actualizar la contraseña.";
            }
        }
    }
}

include("../includes/header.php");
?>

<body>
<?php include("../includes/navbar.php"); ?>

<main class="auth-page">
    <section class="auth-card">
        <p class="section-kicker">Madison Suite</p>
        <h1>Recuperar Contraseña</h1>
        <p class="auth-subtitle">Ingresa tu correo y define una nueva contraseña para volver a entrar.</p>

        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-warning"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Correo electrónico</label>
                <input type="email" name="correo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Nueva contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-4">
                <label>Confirmar contraseña</label>
                <input type="password" name="confirmar" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-warning w-100">Actualizar contraseña</button>
        </form>

        <p class="auth-switch"><a href="login.php">Volver al inicio de sesión</a></p>
    </section>
</main>

<?php include("../includes/footer.php"); ?>
</body>
</html>
