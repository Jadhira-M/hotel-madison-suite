<?php
session_start();
include("includes/header.php");

$secciones = [
    "horarios" => [
        "titulo" => "Hora y Entrada",
        "preguntas" => [
            ["icono" => "clock-fill", "pregunta" => "¿Cuál es el horario de entrada y salida de Madison Suite?", "respuesta" => "Check-in: a partir de las 13:00. Check-out: hasta las 11:30."],
            ["icono" => "calendar-check", "pregunta" => "¿Cuáles son las condiciones de la reserva?", "respuesta" => "La reserva queda registrada al completar tus datos, elegir habitación y seleccionar un método de pago. La confirmación final está sujeta a validación del pago."],
            ["icono" => "arrow-repeat", "pregunta" => "¿Puedo cancelar o modificar mi reserva?", "respuesta" => "Sí. Puedes solicitar cambios comunicándote con el hotel. Toda modificación depende de disponibilidad y condiciones de la reserva."],
        ],
    ],
    "amenidades" => [
        "titulo" => "Amenidades y Servicios",
        "preguntas" => [
            ["icono" => "cup-hot-fill", "pregunta" => "¿Qué tipo de desayuno se sirve en Madison Suite?", "respuesta" => "Se ofrece desayuno continental, americano o buffet según disponibilidad y tipo de reserva."],
            ["icono" => "stars", "pregunta" => "¿Qué se puede hacer en Madison Suite?", "respuesta" => "Ofrece alojamiento cómodo, WiFi, atención permanente y habitaciones con servicios según la categoría seleccionada."],
            ["icono" => "water", "pregunta" => "¿Madison Suite tiene bañera de hidromasaje?", "respuesta" => "Sí, algunas suites cuentan con bañera de hidromasaje o jacuzzi. Revisa el detalle de cada habitación antes de reservar."],
            ["icono" => "safe2-fill", "pregunta" => "¿Dispone el Hotel Madison Suite de caja fuerte?", "respuesta" => "Sí, el hotel dispone de caja fuerte para mayor seguridad de los huéspedes."],
        ],
    ],
    "ubicacion" => [
        "titulo" => "Ubicación y Precios",
        "preguntas" => [
            ["icono" => "geo-alt-fill", "pregunta" => "¿Dónde está situado Hotel Madison Suite?", "respuesta" => "Está situado en Avenida Tarapacá 394 A, Tacna - Perú."],
            ["icono" => "signpost-split-fill", "pregunta" => "¿A qué distancia está Madison Suite del centro de Tacna?", "respuesta" => "Está aproximadamente a 1,5 km del centro de Tacna."],
            ["icono" => "star-fill", "pregunta" => "¿Qué categoría tiene Hotel Madison Suite?", "respuesta" => "El hotel cuenta con categoría de 2 estrellas."],
            ["icono" => "cash-coin", "pregunta" => "¿Las tarifas son por persona o por habitación?", "respuesta" => "Las tarifas publicadas son por habitación y por noche, respetando la capacidad máxima indicada."],
        ],
    ],
];
?>

<body>

<?php include("includes/navbar.php"); ?>

<style>
.faq-page{background:#f7f5ef;min-height:60vh}
.faq-title-band{background:linear-gradient(90deg,#c7962a,#f2d36b,#c7962a);padding:16px;text-align:center}
.faq-title-band h1{color:#050505;font-family:Georgia,serif;font-size:42px;font-weight:900;margin:0}
.faq-shell{max-width:1180px;padding:0 16px 56px}
.faq-tabs{background:#111;display:grid;grid-template-columns:repeat(3,1fr);gap:0;margin:0 -16px 34px;padding:22px 16px 0}
.faq-tabs button{background:transparent;border:0;border-bottom:4px solid transparent;color:white;font-family:Georgia,serif;font-size:25px;font-weight:900;padding:0 14px 18px;text-align:center}
.faq-tabs button.active{border-color:#d4a72c;color:#f2d36b}
.faq-panel{display:none}
.faq-panel.active{align-items:start;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px}
.faq-item{background:white;border:2px solid #b77916;border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,.12);overflow:hidden}
.faq-item summary{align-items:flex-start;color:#111;cursor:pointer;display:flex;font-size:16px;font-weight:900;gap:12px;justify-content:space-between;line-height:1.2;list-style:none;min-height:96px;padding:18px}
.faq-item summary::-webkit-details-marker{display:none}
.faq-item summary span{align-items:flex-start;display:flex;gap:12px}
.faq-item summary span i{color:#c88718;flex:0 0 auto;font-size:30px}
.faq-chevron{color:#c88718;flex:0 0 auto;transition:transform .2s ease}
.faq-item[open] .faq-chevron{transform:rotate(180deg)}
.faq-item p{color:#111;font-size:16px;line-height:1.5;margin:0;padding:0 18px 18px 60px;text-align:justify}
.faq-help{background:#111;border-top:4px solid #d4a72c;color:white;margin-top:38px;padding:34px 28px;text-align:center}
.faq-help h2{color:#f2d36b;font-family:Georgia,serif;font-size:32px;font-weight:900;margin:0 0 12px}
.faq-help p{font-size:18px;line-height:1.6;margin:0 auto 24px;max-width:650px}
.faq-help a{background:linear-gradient(90deg,#b87a20,#f1d26a,#b87a20);border-radius:10px;box-shadow:0 7px 18px rgba(0,0,0,.25);color:#111;display:inline-flex;font-weight:900;justify-content:center;min-width:230px;padding:12px 22px;text-decoration:none}
@media(max-width:900px){.faq-title-band h1{font-size:32px}.faq-tabs,.faq-panel.active{grid-template-columns:1fr}.faq-tabs{margin-inline:0}.faq-help h2{font-size:26px}}
</style>

<main class="faq-page">
    <section class="faq-title-band">
        <h1>Preguntas Frecuentes - Madison Suite</h1>
    </section>

    <section class="container faq-shell">
        <div class="faq-tabs" role="tablist">
            <?php $primera = true; ?>
            <?php foreach ($secciones as $id => $seccion): ?>
                <button class="<?php echo $primera ? "active" : ""; ?>" type="button" data-faq-tab="<?php echo htmlspecialchars($id); ?>">
                    <?php echo htmlspecialchars($seccion["titulo"]); ?>
                </button>
                <?php $primera = false; ?>
            <?php endforeach; ?>
        </div>

        <?php $primera = true; ?>
        <?php foreach ($secciones as $id => $seccion): ?>
            <section class="faq-panel <?php echo $primera ? "active" : ""; ?>" data-faq-panel="<?php echo htmlspecialchars($id); ?>">
                <?php foreach ($seccion["preguntas"] as $pregunta): ?>
                    <details class="faq-item">
                        <summary>
                            <span>
                                <i class="bi bi-<?php echo htmlspecialchars($pregunta["icono"]); ?>"></i>
                                <?php echo htmlspecialchars($pregunta["pregunta"]); ?>
                            </span>
                            <i class="bi bi-chevron-down faq-chevron"></i>
                        </summary>
                        <p><?php echo htmlspecialchars($pregunta["respuesta"]); ?></p>
                    </details>
                <?php endforeach; ?>
            </section>
            <?php $primera = false; ?>
        <?php endforeach; ?>

        <section class="faq-help">
            <h2>¿Sigues buscando?</h2>
            <p>Podemos contestar a la mayoría de las preguntas al momento. Escríbenos y te ayudaremos con reservas, habitaciones, servicios o cualquier detalle de tu estadía.</p>
            <a href="contacto.php">Hacer una pregunta</a>
        </section>
    </section>
</main>

<script>
document.querySelectorAll("[data-faq-tab]").forEach(button => {
    button.addEventListener("click", () => {
        const target = button.dataset.faqTab;
        document.querySelectorAll("[data-faq-tab]").forEach(tab => tab.classList.toggle("active", tab === button));
        document.querySelectorAll("[data-faq-panel]").forEach(panel => {
            panel.classList.toggle("active", panel.dataset.faqPanel === target);
        });
    });
});

document.querySelectorAll(".faq-item").forEach(item => {
    item.addEventListener("toggle", () => {
        if (!item.open) {
            return;
        }

        const panel = item.closest("[data-faq-panel]");
        panel.querySelectorAll(".faq-item").forEach(otherItem => {
            if (otherItem !== item) {
                otherItem.open = false;
            }
        });
    });
});
</script>

<?php include("includes/footer.php"); ?>

</body>
</html>
