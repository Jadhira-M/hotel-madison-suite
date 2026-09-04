<?php
$adminCurrentPage = basename($_SERVER["PHP_SELF"]);

function adminActive($file, $current) {
    return $file === $current ? " active" : "";
}
?>

<div class="admin-layout">

<aside class="admin-sidebar">

<ul class="nav flex-column">

<li class="nav-item">
<a href="../admin/dashboard.php" class="nav-link<?php echo adminActive("dashboard.php", $adminCurrentPage); ?>">
<i class="fa-solid fa-border-all"></i>
Dashboard
</a>
</li>

<li class="nav-item">
<a href="../admin/reservas.php" class="nav-link<?php echo adminActive("reservas.php", $adminCurrentPage); ?>">
<i class="fa-solid fa-clipboard-list"></i>
Reservas
</a>
</li>

<li class="nav-item">
<a href="../admin/habitaciones.php" class="nav-link<?php echo adminActive("habitaciones.php", $adminCurrentPage); ?>">
<i class="fa-solid fa-bed"></i>
Habitaciones
</a>
</li>

<li class="nav-item">
<a href="../admin/usuarios.php" class="nav-link<?php echo adminActive("usuarios.php", $adminCurrentPage); ?>">
<i class="fa-solid fa-users"></i>
Usuarios
</a>
</li>

<li class="nav-item">
<a href="../admin/calendario.php" class="nav-link<?php echo adminActive("calendario.php", $adminCurrentPage); ?>">
<i class="fa-solid fa-calendar-days"></i>
Calendario
</a>
</li>

<li class="nav-item">
<a href="../admin/tarifas.php" class="nav-link<?php echo adminActive("tarifas.php", $adminCurrentPage); ?>">
<i class="fa-solid fa-dollar-sign"></i>
Tarifas
</a>
</li>

<li class="nav-item">
<a href="../admin/reclamaciones.php" class="nav-link<?php echo adminActive("reclamaciones.php", $adminCurrentPage); ?>">
<i class="fa-regular fa-message"></i>
Reclamaciones
</a>
</li>

<li class="nav-item">
<a href="../admin/galeria.php" class="nav-link<?php echo adminActive("galeria.php", $adminCurrentPage); ?>">
<i class="fa-solid fa-images"></i>
Galería
</a>
</li>

<li class="nav-item">
<a href="../admin/resenas.php" class="nav-link<?php echo adminActive("resenas.php", $adminCurrentPage); ?>">
<i class="fa-solid fa-star"></i>
Reseñas
</a>
</li>

<li class="nav-item">
<a href="../admin/inventario.php" class="nav-link<?php echo adminActive("inventario.php", $adminCurrentPage); ?>">
<i class="fa-solid fa-bullseye"></i>
Inventario
</a>
</li>

<li class="nav-item">
<a href="../admin/clientes.php" class="nav-link<?php echo adminActive("clientes.php", $adminCurrentPage); ?>">
<i class="fa-solid fa-user-group"></i>
Clientes
</a>
</li>

</ul>

</aside>

<main class="admin-content">
