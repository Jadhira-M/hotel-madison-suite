<?php
session_start();
include("../config/conexion.php");

$mensaje = "";
$tipoMensaje = "warning";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $passwordPlano = $_POST["password"] ?? "";
    $confirmar = $_POST["confirmar_password"] ?? "";

    if ($passwordPlano !== $confirmar) {
        $mensaje = "Las contraseÃ±as no coinciden.";
    } else {
        $password = password_hash($passwordPlano, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre, correo, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nombre, $correo, $password);

        if ($stmt->execute()) {
            $nuevoUsuarioId = $stmt->insert_id;

            session_regenerate_id(true);
            $_SESSION["id_usuario"] = $nuevoUsuarioId;
            $_SESSION["usuario"] = $nombre;
            $_SESSION["correo"] = $correo;
            $_SESSION["rol"] = "cliente";

            header("Location: ../reservas/reservar.php");
            exit();
        } else {
            $mensaje = "No se pudo crear la cuenta. Revisa si el correo ya existe.";
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

<h1>Crear una Cuenta</h1>

<p class="auth-subtitle">
Ãšnete a Madison Suite y disfruta de beneficios exclusivos.
</p>

<?php if ($mensaje !== "") { ?>

<div class="alert alert-<?php echo $tipoMensaje; ?>">
<?php echo htmlspecialchars($mensaje); ?>
</div>

<?php } ?>

<form action="" method="POST">

<div class="mb-3">

<label>Nombre completo</label>

<input
type="text"
name="nombre"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Correo electrÃ³nico</label>

<input
type="email"
name="correo"
class="form-control"
required>

</div>

<div class="mb-3">

<label>ContraseÃ±a</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<div class="mb-4">

<label>Confirmar contraseÃ±a</label>

<input
type="password"
name="confirmar_password"
class="form-control"
required>

</div>

<button type="submit" class="btn btn-warning w-100">
Registrarse
</button>

</form>

<p class="auth-switch">
Â¿Ya tienes una cuenta?
<a href="login.php">Iniciar sesiÃ³n</a>
</p>

</section>

</main>

<?php include("../includes/footer.php"); ?>

</body>

</html>
