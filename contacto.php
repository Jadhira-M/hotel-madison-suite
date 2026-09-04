<?php
session_start();

$contactFlash = null;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $asunto = trim($_POST["asunto"] ?? "");
    $mensaje = trim($_POST["mensaje"] ?? "");

    if ($nombre === "" || $correo === "" || $asunto === "" || $mensaje === "") {
        $contactFlash = ["type" => "warning", "text" => "Completa todos los campos para enviar tu mensaje."];
    } else {
        $contactFlash = ["type" => "success", "text" => "Gracias por escribirnos. Hemos recibido tu mensaje y te responderemos pronto."];
    }
}

include("includes/header.php");
?>

<body>

<?php include("includes/navbar.php"); ?>

<section class="bg-dark text-white py-5">
    <div class="container text-center">
        <h1 class="display-4">Contáctanos</h1>
        <p>Estamos disponibles para ayudarte.</p>
    </div>
</section>

<section class="py-5 contact-page">
    <div class="container">
        <?php if ($contactFlash): ?>
            <div class="alert alert-<?php echo htmlspecialchars($contactFlash["type"]); ?>">
                <?php echo htmlspecialchars($contactFlash["text"]); ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-5">
                <h2>Información</h2>
                <hr>

                <p><i class="bi bi-geo-alt-fill"></i> Av. Tarapacá 394 A<br>Tacna - Perú</p>
                <p><i class="bi bi-telephone-fill"></i> +51 939188087</p>
                <p><i class="bi bi-envelope-fill"></i> hotelmadisonsuite@gmail.com</p>
                <p><i class="bi bi-clock-fill"></i> Atención 24 horas</p>

                <div class="immediate-help-card">
                    <h3>¿Necesitas ayuda inmediata?</h3>
                    <p>
                        Nuestro equipo está disponible 24/7 para atenderte. Puedes llamarnos o escribirnos por WhatsApp.
                    </p>
                    <a href="https://wa.me/51939188087" target="_blank" rel="noopener">
                        <i class="bi bi-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>

            <div class="col-lg-7">
                <form method="POST">
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Correo</label>
                        <input type="email" name="correo" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Asunto</label>
                        <input type="text" name="asunto" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Mensaje</label>
                        <textarea rows="5" name="mensaje" class="form-control" required></textarea>
                    </div>

                    <button class="btn btn-warning" type="submit">Enviar Mensaje</button>
                </form>
            </div>
        </div>
    </div>
</section>

<section>
    <iframe
        src="https://www.google.com/maps?q=Tacna&output=embed"
        width="100%"
        height="450"
        style="border:0;"
        loading="lazy">
    </iframe>
</section>

<?php include("includes/footer.php"); ?>

</body>

</html>
