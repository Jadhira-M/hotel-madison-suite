<?php
session_start();
include("includes/header.php");
?>

<body>

<?php include("includes/navbar.php"); ?>

<main class="location-page">

<section class="container location-shell">

<h1>Ubicación</h1>

<div class="location-main-grid">

<div class="location-map-large">

<iframe
src="https://www.google.com/maps?q=Av.%20Tarapaca%20394%20A%20Tacna%20Peru&output=embed"
loading="lazy"
referrerpolicy="no-referrer-when-downgrade">
</iframe>

</div>

<div class="location-info-stack">

<article class="location-info-card">
<i class="bi bi-geo-alt-fill"></i>
<div>
<h2>Dirección</h2>
<p>Av. Tarapacá 394 A<br>Tacna - Perú</p>
</div>
</article>

<article class="location-info-card">
<i class="bi bi-send-fill"></i>
<div>
<h2>Cómo llegar</h2>
<p>Ubicados en una de las avenidas principales de Tacna, con fácil acceso desde cualquier punto de la ciudad.</p>
</div>
</article>

</div>

</div>

<h2 class="references-title">Puntos de Referencia Cercanos</h2>

<div class="reference-grid">

<article>
<i class="bi bi-bank"></i>
<span>Arco Parabólico</span>
<strong>2.5 km</strong>
</article>

<article>
<i class="bi bi-building"></i>
<span>Plaza de Armas</span>
<strong>1.8 km</strong>
</article>

<article>
<i class="bi bi-bus-front"></i>
<span>Terminal Terrestre</span>
<strong>3.5 km</strong>
</article>

<article>
<i class="bi bi-shop"></i>
<span>Zona Comercial</span>
<strong>500 m</strong>
</article>

<article>
<i class="bi bi-airplane"></i>
<span>Aeropuerto</span>
<strong>6.0 km</strong>
</article>

<article>
<i class="bi bi-cup-hot"></i>
<span>Restaurantes</span>
<strong>200 m</strong>
</article>

</div>

<section class="terminal-directions">

<h2>Instrucciones desde el Terminal</h2>

<ol>
<li>Salir del Terminal Terrestre de Tacna.</li>
<li>Tomar un taxi hacia Av. Tarapacá, aproximadamente 10 minutos.</li>
<li>El hotel está en la avenida principal, fácil de ubicar.</li>
</ol>

</section>

</section>

</main>

<?php include("includes/footer.php"); ?>

</body>

</html>
