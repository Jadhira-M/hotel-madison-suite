<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../includes/header.php");
?>

<body>
<?php include("../includes/navbar.php"); ?>

<main class="container py-5">
    <div class="mb-5">
        <p class="section-kicker">Mi cuenta</p>
        <h1 class="fw-bold">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
        <p class="text-muted">Desde aqu&iacute; puedes revisar tus reservas, reclamos y datos personales.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-xl-3">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check fs-1 text-warning"></i>
                    <h4 class="mt-3">Mis Reservas</h4>
                    <p>Consulta tus reservas actuales.</p>
                    <a href="../reservas/mis_reservas.php" class="btn btn-warning">Ver</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-clock-history fs-1 text-warning"></i>
                    <h4 class="mt-3">Historial</h4>
                    <p>Revisa tus reservas anteriores.</p>
                    <a href="historial.php" class="btn btn-warning">Ver historial</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-circle fs-1 text-warning"></i>
                    <h4 class="mt-3">Mis Reclamos</h4>
                    <p>Consulta el estado de tus reclamos.</p>
                    <a href="../reclamaciones/mis_reclamos.php" class="btn btn-warning">Ver reclamos</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-person-circle fs-1 text-warning"></i>
                    <h4 class="mt-3">Mi Perfil</h4>
                    <p>Consulta tus datos personales.</p>
                    <a href="perfil.php" class="btn btn-warning">Ver perfil</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-shield-lock fs-1 text-warning"></i>
                    <h4 class="mt-3">Cambiar Contraseña</h4>
                    <p>Actualiza tu contraseña de acceso.</p>
                    <a href="cambiar_password.php" class="btn btn-warning">Cambiar</a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include("../includes/footer.php"); ?>
</body>
</html>
