<?php
session_start();

include("includes/header.php");
?>

<body>

<?php include("includes/navbar.php"); ?>

<!-- ===========================
        HERO PRINCIPAL
=========================== -->

<section class="hero">

    <div class="overlay"></div>

    <div class="container">

        <div class="row align-items-center vh-100">

            <div class="col-lg-6">

                <h5 class="text-warning fw-bold mb-3">
                    BIENVENIDO A
                </h5>

                <h1 class="display-2 fw-bold text-white">

                    Madison Suite

                </h1>

                <h3 class="text-white mb-4">

                    TU HOGAR EN TACNA

                </h3>

                <p class="text-light mb-4">

                    Vive una experiencia única con habitaciones
                    cómodas, atención personalizada y el mejor
                    servicio para hacer de tu estadía un momento
                    inolvidable.

                </p>

                <a href="<?php echo isset($_SESSION['id_usuario']) ? 'reservas/reservar.php' : 'auth/login.php?redirect=../reservas/reservar.php'; ?>"
                   class="btn btn-warning btn-lg me-3">
                    <i class="bi bi-calendar-check"></i>

                    Reservar Ahora

                </a>

                <a href="nosotros.php"
                   class="btn btn-outline-light btn-lg">

                   <i class="bi bi-building"></i>

                    Conócenos

                </a>

            </div>

        </div>

    </div>

</section>

<!-- =============================
        HABITACIONES
============================= -->

<section class="container py-5">

<div class="text-center mb-5">

<h2 class="fw-bold">

Habitaciones Destacadas

</h2>

<p class="text-muted">

Descubre nuestras habitaciones más solicitadas.

</p>

</div>

<div class="row">

<?php

include("config/conexion.php");

$sql="SELECT * FROM habitaciones
LIMIT 3";

$resultado=$conn->query($sql);

while($fila=$resultado->fetch_assoc()){

?>

<div class="col-lg-4 col-md-6 mb-4">

<div class="room-card">

<img
src="assets/img/habitaciones/<?php echo $fila['imagen'];?>">

<div class="room-body">

<h3>

<?php echo $fila['nombre'];?>

</h3>

<p>

<?php echo $fila['descripcion'];?>

</p>

<h4>

S/. <?php echo $fila['precio'];?>

</h4>

<?php $featuredReservaUrl = 'reservas/reservar.php?id=' . $fila['id']; ?>
<a
href="<?php echo isset($_SESSION['id_usuario']) ? $featuredReservaUrl : 'auth/login.php?redirect=../' . urlencode($featuredReservaUrl); ?>"
class="btn btn-warning w-100">

Reservar

</a>

</div>

</div>

</div>

<?php

}

?>

</div>

</section>

<!-- ===========================
        CARACTERÍSTICAS
=========================== -->

<section class="py-5">

<div class="container">

<div class="row text-center">

<div class="col-md-3">

<div class="feature-card">

<i class="bi bi-shield-check"></i>

<h4>

Seguridad 24/7

</h4>

<p>

Personal capacitado las 24 horas.

</p>

</div>

</div>

<div class="col-md-3">

<div class="feature-card">

<i class="bi bi-stars"></i>

<h4>

Limpieza

</h4>

<p>

Habitaciones impecables.

</p>

</div>

</div>

<div class="col-md-3">

<div class="feature-card">

<i class="bi bi-house-heart"></i>

<h4>

Confort

</h4>

<p>

Ambientes modernos y acogedores.

</p>

</div>

</div>

<div class="col-md-3">

<div class="feature-card">

<i class="bi bi-award"></i>

<h4>

7 años

</h4>

<p>

Brindando la mejor experiencia.

</p>

</div>

</div>

</div>

</div>

</section>

<!-- ===========================
        SOBRE NOSOTROS
=========================== -->

<section class="about-section">

<div class="container">

<div class="row align-items-center">

<div class="col-lg-6">

<img src="assets/img/hotel.jpg"
class="img-fluid rounded shadow">

</div>

<div class="col-lg-6">

<h2>

¿Por qué elegir Madison Suite?

</h2>

<p>

Nos enfocamos en ofrecer una experiencia
cómoda y segura para cada huésped.

Contamos con habitaciones equipadas,
wifi gratuito, atención personalizada,
ambientes modernos y una excelente
ubicación en la ciudad de Tacna.

</p>

<a href="nosotros.php"
class="btn btn-dark">

Leer más

</a>

</div>

</div>

</div>

</section>

<!-- ===========================
        LLAMADO A LA ACCIÓN
=========================== -->

<section class="cta">

<div class="container text-center">

<h2>

¿Listo para disfrutar tu estadía?

</h2>

<p>

Reserva hoy mismo y vive una experiencia
inolvidable.

</p>

<a href="<?php echo isset($_SESSION['id_usuario']) ? 'reservas/reservar.php' : 'auth/login.php'; ?>"
class="btn btn-warning btn-lg">

Reservar Habitación

</a>

</div>

</section>

<?php include("includes/footer.php"); ?>

</body>

</html>
