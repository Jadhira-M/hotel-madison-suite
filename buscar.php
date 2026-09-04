<?php
session_start();
include("includes/header.php");
include("config/conexion.php");

$query = trim($_GET["q"] ?? "");
$queryLower = mb_strtolower($query, "UTF-8");

function cleanRoomDescription($descripcion)
{
    $descripcion = trim((string) $descripcion);
    $replacements = [
        ", aire acondicionado" => "",
        ", con aire acondicionado" => "",
        "aire acondicionado y " => "",
        "aire acondicionado" => "",
        "Aire acondicionado y " => "",
        "Aire acondicionado" => "",
        "  " => " ",
    ];

    return trim(str_replace(array_keys($replacements), array_values($replacements), $descripcion));
}

$paginas = [
    [
        "titulo" => "Inicio",
        "descripcion" => "Bienvenida, reserva rápida y presentación principal de Madison Suite.",
        "url" => "index.php",
        "keywords" => "inicio home reservar ahora conocenos hotel madison suite tacna",
        "icono" => "bi-house-door",
    ],
    [
        "titulo" => "Nosotros",
        "descripcion" => "Historia, misión, visión y valores del hotel.",
        "url" => "nosotros.php",
        "keywords" => "nosotros historia mision vision valores respeto calidad seguridad compromiso",
        "icono" => "bi-building",
    ],
    [
        "titulo" => "Habitaciones",
        "descripcion" => "Habitaciones disponibles, precios, detalles y opción de reserva.",
        "url" => "habitaciones.php",
        "keywords" => "habitaciones cuartos rooms reservar precio doble triple matrimonial familiar suite",
        "icono" => "bi-door-open",
    ],
    [
        "titulo" => "Servicios",
        "descripcion" => "Servicios del hotel, habitación, restaurante y ubicación detallada.",
        "url" => "servicios.php",
        "keywords" => "servicios wifi cochera desayuno hidromasaje jacuzzi restaurante minibar recepcion",
        "icono" => "bi-stars",
    ],
    [
        "titulo" => "Normas",
        "descripcion" => "Horarios, cancelación, camas para niños, mascotas y tarjetas aceptadas.",
        "url" => "normas.php",
        "keywords" => "normas reglas check-in check-out cancelacion prepago mascotas niños tarjetas",
        "icono" => "bi-clipboard-check",
    ],
    [
        "titulo" => "Galería",
        "descripcion" => "Fotos del hotel, habitaciones, restaurante y espacios comunes.",
        "url" => "galeria.php",
        "keywords" => "galeria fotos imagenes hotel habitaciones restaurante desayuno fachada",
        "icono" => "bi-images",
    ],
    [
        "titulo" => "Reseñas",
        "descripcion" => "Opiniones de huéspedes, calificaciones y comentarios.",
        "url" => "resenas.php",
        "keywords" => "reseñas opiniones comentarios calificaciones huéspedes rating experiencia",
        "icono" => "bi-star",
    ],
    [
        "titulo" => "Contacto",
        "descripcion" => "Dirección, teléfono, correo, WhatsApp y formulario de contacto.",
        "url" => "contacto.php",
        "keywords" => "contacto telefono correo whatsapp direccion ayuda mensaje",
        "icono" => "bi-telephone",
    ],
    [
        "titulo" => "Libro de reclamaciones",
        "descripcion" => "Formulario para registrar quejas o reclamos.",
        "url" => "reclamaciones/libro.php",
        "keywords" => "libro reclamaciones queja reclamo consumidor incidencia",
        "icono" => "bi-journal-text",
    ],
    [
        "titulo" => "Preguntas frecuentes",
        "descripcion" => "Respuestas sobre reservas, servicios, ubicación y precios.",
        "url" => "faq.php",
        "keywords" => "preguntas frecuentes faq reserva servicios ubicación precios",
        "icono" => "bi-question-circle",
    ],
    [
        "titulo" => "Política de privacidad",
        "descripcion" => "Uso y protección de datos personales.",
        "url" => "privacidad.php",
        "keywords" => "privacidad datos personales informacion derechos usuario",
        "icono" => "bi-shield-check",
    ],
    [
        "titulo" => "Comprobantes electrónicos",
        "descripcion" => "Información sobre boleta, factura y comprobantes.",
        "url" => "comprobantes.php",
        "keywords" => "comprobantes electronicos boleta factura ruc pagos",
        "icono" => "bi-receipt",
    ],
];

$resultadosPaginas = [];
if ($query !== "") {
    foreach ($paginas as $pagina) {
        $texto = mb_strtolower($pagina["titulo"] . " " . $pagina["descripcion"] . " " . $pagina["keywords"], "UTF-8");
        if (str_contains($texto, $queryLower)) {
            $resultadosPaginas[] = $pagina;
        }
    }
}

$resultadosHabitaciones = [];
if ($query !== "" && isset($conn)) {
    $like = "%" . $query . "%";
    $stmt = $conn->prepare("SELECT id, nombre, tipo, descripcion, precio, capacidad, imagen FROM habitaciones WHERE nombre LIKE ? OR tipo LIKE ? OR descripcion LIKE ? ORDER BY nombre ASC");
    if ($stmt) {
        $stmt->bind_param("sss", $like, $like, $like);
        $stmt->execute();
        $resultadosHabitaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<body>

<?php include("includes/navbar.php"); ?>

<main class="search-page">
    <section class="search-hero">
        <div class="container">
            <p>Madison Suite</p>
            <h1>Buscar en el sitio</h1>
            <form class="search-main-form" action="buscar.php" method="GET">
                <input type="search" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Ej. habitaciones, wifi, reclamos, desayuno..." autofocus>
                <button type="submit"><i class="bi bi-search"></i> Buscar</button>
            </form>
        </div>
    </section>

    <section class="container search-results">
        <?php if ($query === ""): ?>
            <div class="search-empty">
                <i class="bi bi-search"></i>
                <h2>Escribe una palabra para buscar</h2>
                <p>Puedes buscar habitaciones, servicios, reservas, reclamos, galería o información del hotel.</p>
            </div>
        <?php else: ?>
            <div class="search-count">
                <h2>Resultados para "<?php echo htmlspecialchars($query); ?>"</h2>
                <p><?php echo count($resultadosPaginas) + count($resultadosHabitaciones); ?> resultado(s) encontrado(s)</p>
            </div>

            <?php if ($resultadosHabitaciones): ?>
                <h3 class="search-section-title">Habitaciones encontradas</h3>
                <div class="search-room-grid">
                    <?php foreach ($resultadosHabitaciones as $habitacion): ?>
                        <article class="search-room-card">
                            <img src="assets/img/<?php echo htmlspecialchars($habitacion["imagen"] ?: "hotel.jpg"); ?>" alt="<?php echo htmlspecialchars($habitacion["nombre"]); ?>">
                            <div>
                                <h4><?php echo htmlspecialchars($habitacion["nombre"]); ?></h4>
                                <p><?php echo htmlspecialchars(cleanRoomDescription($habitacion["descripcion"])); ?></p>
                                <strong>S/ <?php echo number_format((float) $habitacion["precio"], 2); ?></strong>
                                <a href="habitacion_detalle.php?id=<?php echo (int) $habitacion["id"]; ?>">Ver habitación</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($resultadosPaginas): ?>
                <h3 class="search-section-title">Páginas relacionadas</h3>
                <div class="search-link-list">
                    <?php foreach ($resultadosPaginas as $pagina): ?>
                        <a href="<?php echo htmlspecialchars($pagina["url"]); ?>" class="search-link-card">
                            <i class="bi <?php echo htmlspecialchars($pagina["icono"]); ?>"></i>
                            <span>
                                <strong><?php echo htmlspecialchars($pagina["titulo"]); ?></strong>
                                <small><?php echo htmlspecialchars($pagina["descripcion"]); ?></small>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!$resultadosPaginas && !$resultadosHabitaciones): ?>
                <div class="search-empty">
                    <i class="bi bi-emoji-neutral"></i>
                    <h2>No encontramos resultados</h2>
                    <p>Prueba con palabras como "habitación", "wifi", "reserva", "contacto" o "reclamo".</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>

<?php include("includes/footer.php"); ?>

</body>
</html>
