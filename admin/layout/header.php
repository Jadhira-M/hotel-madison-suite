<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['id_usuario']) ||
    !isset($_SESSION['rol']) ||
    $_SESSION['rol'] !== 'admin'
) {
    header("Location: ../auth/login.php");
    exit();
}

$projectBase = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/\\');
$adminCssPath = __DIR__ . "/../../assets/css/admin.css";
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Panel Administrador</title>

<link rel="icon" type="image/png" href="<?php echo $projectBase; ?>/assets/img/emblema.png">
<link rel="shortcut icon" type="image/png" href="<?php echo $projectBase; ?>/assets/img/emblema.png">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="<?php echo $projectBase; ?>/assets/css/admin.css?v=admin-figma-2">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<!-- DataTables CSS -->
<link rel="stylesheet"
href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<?php if (is_file($adminCssPath)) { ?>
<style>
<?php readfile($adminCssPath); ?>
</style>
<?php } ?>

</head>

<body>

<header class="admin-topbar">

<a href="dashboard.php" class="admin-brand">
<img src="<?php echo $projectBase; ?>/assets/img/logo.png" alt="Madison Suite">
</a>

<nav class="admin-public-nav">
<a href="../index.php">Inicio</a>
<a href="../nosotros.php">Nosotros</a>
<a href="../habitaciones.php">Habitaciones</a>
<a href="../servicios.php">Servicios</a>
<a href="../resenas.php">Reseñas</a>
<a href="../contacto.php">Contacto</a>
</nav>

<div class="admin-session-actions">
<span><i class="bi bi-person-circle"></i> Admin</span>
<a href="../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Salir</a>
</div>

</header>

