<?php
session_start();
include("includes/header.php");
?>

<body>

<?php include("includes/navbar.php"); ?>

<section class="services-design">

<div class="services-title-band">

<h1>Nuestros servicios exclusivos</h1>

<p>Disfruta de la comodidad y atención premium en Tacna.</p>

</div>

<div class="container services-shell">

<div class="section-heading">
<span></span>
<h2>Servicios del Hotel</h2>
<span></span>
</div>

<div class="hotel-service-grid">

<article class="hotel-service-item">
<i class="bi bi-clock-history"></i>
<strong>24h</strong>
<span>Recepción</span>
</article>

<article class="hotel-service-item">
<i class="bi bi-car-front"></i>
<strong>Cochera</strong>
<span>Gratis</span>
</article>

<article class="hotel-service-item">
<i class="bi bi-wifi"></i>
<strong>WiFi</strong>
<span>Incluido</span>
</article>

<article class="hotel-service-item">
<i class="bi bi-suitcase-lg"></i>
<strong>Custodia de</strong>
<span>Equipaje</span>
</article>

</div>

<div class="section-heading">
<span></span>
<h2>Servicios de Habitación</h2>
<span></span>
</div>

<div class="room-service-layout">

<img src="assets/img/servicios/limpieza.jpg"
alt="Habitación de Madison Suite"
class="room-service-photo">

<div class="room-service-list">

<article>
<i class="bi bi-water"></i>
<div>
<h3>Hidromasaje / Jacuzzi:</h3>
<p>Tina de hidromasaje privada en suites para un descanso profundo y relajante.</p>
</div>
</article>

<article>
<i class="bi bi-tv"></i>
<div>
<h3>TV por cable:</h3>
<p>Pantallas HD con amplia variedad de canales nacionales e internacionales.</p>
</div>
</article>

<article>
<i class="bi bi-cup-straw"></i>
<div>
<h3>Mini-bar:</h3>
<p>Bebidas y snacks variados disponibles directamente en la comodidad de tu habitación.</p>
</div>
</article>

</div>

</div>

<div class="section-heading">
<span></span>
<h2>Restaurante</h2>
<span></span>
</div>

<div class="restaurant-grid">

<article class="restaurant-card">
<img src="assets/img/servicios/desayuno.jpg" alt="Desayuno buffet">
<div>
<h3>Desayuno Buffet</h3>
<p>Variedad de frutas frescas, panes y platos calientes servidos de 07:30 a 09:00.</p>
</div>
</article>

<article class="restaurant-card">
<img src="assets/img/servicios/restaurante.jpg" alt="Tarde de té y café">
<div>
<h3>Tarde de té y café</h3>
<p>Disfrute de una selección de bebidas calientes de cortesía en nuestro horario especial.</p>
</div>
</article>

</div>

<div class="location-preview">

<iframe
src="https://www.google.com/maps?q=Av.%20Tarapaca%20394%20A%20Tacna%20Peru&output=embed"
loading="lazy"
referrerpolicy="no-referrer-when-downgrade">
</iframe>

<div>
<a href="ubicacion.php" class="btn btn-warning">
Ver ubicación detallada
</a>
<a href="contacto.php" class="btn btn-outline-dark">
Más información
</a>
</div>

</div>

</div>

</section>

<?php include("includes/footer.php"); ?>

</body>

</html>
