<?php
session_start();
include("../config/conexion.php");

$error = "";
$redirect = $_GET["redirect"] ?? $_POST["redirect"] ?? "";

function safeRedirect($redirect)
{
    if ($redirect === "" || preg_match("#^(https?:)?//#i", $redirect)) {
        return "../index.php";
    }

    if (strpos($redirect, "..") !== false && strpos($redirect, "../") !== 0) {
        return "../index.php";
    }

    return $redirect;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST["correo"] ?? "");
    $password = $_POST["password"] ?? "";

    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario["password"])) {
            $_SESSION["id_usuario"] = $usuario["id"];
            $_SESSION["usuario"] = $usuario["nombre"];
            $_SESSION["correo"] = $usuario["correo"];
            $_SESSION["rol"] = $usuario["rol"];

            if ($usuario["rol"] == "admin") {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: " . safeRedirect($redirect));
            }
            exit();
        }

        $error = "Contraseña incorrecta.";
    } else {
        $error = "No encontramos una cuenta con ese correo.";
    }
}

include("../includes/header.php");
?>

<body>

<?php include("../includes/navbar.php"); ?>

<main class="auth-page">

<section class="auth-card">

<p class="section-kicker">Madison Suite</p>

<h1>Inicio de Sesión</h1>

<p class="auth-subtitle">
Accede a tu cuenta para reservar y consultar tus solicitudes.
</p>

<?php if ($error !== "") { ?>

<div class="alert alert-warning">
<?php echo htmlspecialchars($error); ?>
</div>

<?php } ?>

<form method="POST">

<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">

<div class="mb-3">

<label>Correo electrónico</label>

<input
type="email"
name="correo"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Contraseña</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<div class="d-flex justify-content-between align-items-center mb-4">

<a href="recuperar.php" class="auth-link">¿Olvidaste tu contraseña?</a>

</div>

<button type="submit" class="btn btn-warning w-100">
Ingresar
</button>

</form>

<p class="auth-switch">
¿Aún no tienes cuenta?
<a href="registro.php">Regístrate</a>
</p>

</section>

</main>

<?php include("../includes/footer.php"); ?>

</body>

</html>
