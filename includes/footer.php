<footer class="footer footer-hotel">

<div class="container">

<div class="footer-top">

<div class="footer-brand">

<img src="<?php echo $baseUrl ?? ""; ?>assets/img/logo.png"
alt="Madison Suite"
class="footer-logo">

<p>
Tu hogar en Tacna, con atenciÃ³n cercana, habitaciones cÃ³modas y ubicaciÃ³n estratÃ©gica.
</p>

</div>

<div class="footer-payments">

<span>Medios de pago</span>

<img src="<?php echo $baseUrl ?? ""; ?>assets/img/visa.png" alt="Visa">

<img src="<?php echo $baseUrl ?? ""; ?>assets/img/mastercard.png" alt="Mastercard">

<img src="<?php echo $baseUrl ?? ""; ?>assets/img/yape.png" alt="Yape">

<img src="<?php echo $baseUrl ?? ""; ?>assets/img/plin.png" alt="Plin">

</div>

</div>

<hr>

<div class="footer-grid">

<div>

<h5>Madison Suite</h5>

<a href="<?php echo $baseUrl ?? ""; ?>nosotros.php">Nosotros</a>

<a href="<?php echo $baseUrl ?? ""; ?>servicios.php">Servicios</a>

<a href="<?php echo $baseUrl ?? ""; ?>normas.php">Normas</a>

<a href="<?php echo $baseUrl ?? ""; ?>galeria.php">GalerÃ­a</a>

<a href="<?php echo $baseUrl ?? ""; ?>resenas.php">ReseÃ±as</a>

<a href="<?php echo $baseUrl ?? ""; ?>contacto.php">Contacto</a>

</div>

<div>

<h5>Reservas</h5>

<p>Central de reservas:<br><strong>+51 939-188-087</strong></p>

<p>Correo:<br><strong>hotelmadisonsuite@gmail.com</strong></p>

</div>

<div>

<h5>AtenciÃ³n al cliente</h5>

<a href="<?php echo $baseUrl ?? ""; ?>reclamaciones/libro.php">Libro de reclamaciones</a>

<a href="<?php echo $baseUrl ?? ""; ?>faq.php">Preguntas frecuentes</a>

<a href="<?php echo $baseUrl ?? ""; ?>privacidad.php">PolÃ­tica de privacidad</a>

<a href="<?php echo $baseUrl ?? ""; ?>comprobantes.php">Comprobantes electrÃ³nicos</a>

</div>

<div>

<h5>Enlaces de interÃ©s</h5>

<a href="<?php echo $baseUrl ?? ""; ?>index.php">Inicio</a>

<a href="<?php echo $baseUrl ?? ""; ?>habitaciones.php">Habitaciones</a>

<a href="<?php echo isset($_SESSION["id_usuario"]) ? ($baseUrl ?? "") . "reservas/reservar.php" : ($baseUrl ?? "") . "auth/login.php?redirect=../reservas/reservar.php"; ?>">Reservar</a>

<a href="<?php echo $baseUrl ?? ""; ?>ubicacion.php">UbicaciÃ³n</a>

<a href="<?php echo $baseUrl ?? ""; ?>galeria.php">GalerÃ­a</a>


<a href="https://www.sunat.gob.pe/"
target="_blank"
rel="noopener">Sunat</a>

<a href="https://www.booking.com/"
target="_blank"
rel="noopener">Booking</a>

<a href="https://www.gob.pe/migraciones"
target="_blank"
rel="noopener">Migraciones</a>
</div>

<div>

<h5>ContÃ¡ctanos</h5>

<p>Av. TarapacÃ¡ 394 A<br>Tacna - PerÃº</p>

<p>AtenciÃ³n 24 horas<br>Todos los dÃ­as</p>

<a href="<?php echo $baseUrl ?? ""; ?>reclamaciones/libro.php"
class="claims-link">

<img src="<?php echo $baseUrl ?? ""; ?>assets/img/libro-reclamaciones.png"
alt="Libro de Reclamaciones">

</a>

</div>

</div>

<div class="footer-map">

<iframe
src="https://www.google.com/maps?q=Av.%20Tarapaca%20394%20A%20Tacna%20Peru&output=embed"
loading="lazy"
referrerpolicy="no-referrer-when-downgrade">
</iframe>

</div>

<p class="footer-copy">
Todos los derechos reservados - Madison Suite
</p>

</div>

</footer>

<a class="floating-whatsapp"
   href="https://wa.me/51939188087"
   target="_blank"
   rel="noopener"
   aria-label="Escribir por WhatsApp"
   style="position:fixed !important; right:24px !important; bottom:24px !important; z-index:99999 !important; width:58px !important; height:58px !important; border-radius:50% !important; background:#25d366 !important; color:#fff !important; display:flex !important; align-items:center !important; justify-content:center !important; text-decoration:none !important; box-shadow:0 10px 24px rgba(0,0,0,.28) !important; border:3px solid #fff !important; font-size:30px !important;">
    <i class="bi bi-whatsapp"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
$i18nPublicPath = __DIR__ . "/../assets/js/i18n-public.js";
$i18nPublicVersion = is_file($i18nPublicPath) ? filemtime($i18nPublicPath) : time();
?>
<script src="<?php echo $baseUrl ?? ""; ?>assets/js/i18n-public.js?v=<?php echo $i18nPublicVersion; ?>"></script>

<?php
$motionPath = __DIR__ . "/../assets/js/motion.js";
$motionVersion = is_file($motionPath) ? filemtime($motionPath) : time();
?>
<script src="<?php echo $baseUrl ?? ""; ?>assets/js/motion.js?v=<?php echo $motionVersion; ?>"></script>
