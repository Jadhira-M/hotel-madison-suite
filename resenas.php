<?php
session_start();
include("includes/header.php");
include("config/conexion.php");

function tableExists($conn, $table)
{
    $database = $conn->query("SELECT DATABASE() AS db")->fetch_assoc()["db"] ?? "";
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = ?
          AND TABLE_NAME = ?
    ");
    $stmt->bind_param("ss", $database, $table);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return (int) ($row["total"] ?? 0) > 0;
}

function reviewStars($score)
{
    $stars = (int) round(((float) $score) / 2);
    return str_repeat("★", $stars) . str_repeat("☆", max(0, 5 - $stars));
}

function reviewLabel($score)
{
    $score = (float) $score;
    if ($score >= 8.5) {
        return "Excelente";
    }
    if ($score >= 7) {
        return "Muy bueno";
    }
    if ($score >= 5) {
        return "Normal";
    }
    return "Por mejorar";
}

function reviewTypeWhere($filter)
{
    if ($filter === "buenas") {
        return " AND puntuacion >= 8";
    }
    if ($filter === "intermedias") {
        return " AND puntuacion BETWEEN 5 AND 7";
    }
    if ($filter === "malas") {
        return " AND puntuacion < 5";
    }
    return "";
}

$reviewsReady = tableExists($conn, "resenas");
$filter = $_GET["tipo"] ?? "todas";
$sort = $_GET["orden"] ?? "recientes";
$validFilters = ["todas", "buenas", "intermedias", "malas"];
$validSorts = ["recientes", "mejor", "menor"];

if (!in_array($filter, $validFilters, true)) {
    $filter = "todas";
}
if (!in_array($sort, $validSorts, true)) {
    $sort = "recientes";
}

$orderSql = "creado_en DESC";
if ($sort === "mejor") {
    $orderSql = "puntuacion DESC, creado_en DESC";
} elseif ($sort === "menor") {
    $orderSql = "puntuacion ASC, creado_en DESC";
}

$summary = [
    "total" => 3,
    "promedio" => 8.4,
    "positivas" => 2,
    "intermedias" => 1,
    "malas" => 0,
    "porcentaje" => 67,
];

$categories = [
    "Personal" => 9.1,
    "Instalaciones y servicios" => 8.2,
    "Limpieza" => 8.5,
    "Confort" => 8.5,
    "Relación calidad-precio" => 8.6,
    "Ubicación" => 7.4,
    "WiFi gratis" => 8.0,
];

$reviews = [
    [
        "nombre" => "María G.",
        "pais" => "Perú",
        "tipo_viaje" => "Viaje familiar",
        "habitacion" => "Habitación Familiar",
        "noches" => 2,
        "puntuacion" => 9.0,
        "titulo" => "Excelente",
        "comentario" => "El desayuno y la habitación estaban muy confortables. La atención fue cordial desde la llegada.",
        "lo_mejor" => "La atención del personal",
        "mejorar" => "Sin comentarios",
        "creado_en" => "2026-02-24 10:00:00",
    ],
    [
        "nombre" => "Carlos R.",
        "pais" => "Chile",
        "tipo_viaje" => "Estadía de trabajo",
        "habitacion" => "Habitación Doble",
        "noches" => 1,
        "puntuacion" => 8.0,
        "titulo" => "Muy bueno",
        "comentario" => "Me gustó que cuentan con cochera y que el personal siempre estuvo atento.",
        "lo_mejor" => "La cochera y la ubicación",
        "mejorar" => "Mayor variedad en desayuno",
        "creado_en" => "2026-02-18 11:00:00",
    ],
    [
        "nombre" => "Ana T.",
        "pais" => "Perú",
        "tipo_viaje" => "Fin de semana",
        "habitacion" => "Suite Familiar",
        "noches" => 3,
        "puntuacion" => 8.2,
        "titulo" => "Muy bueno",
        "comentario" => "Muy buen desayuno, estacionamiento cómodo y un ambiente tranquilo para descansar.",
        "lo_mejor" => "Ambiente tranquilo",
        "mejorar" => "Sin comentarios",
        "creado_en" => "2026-02-10 09:00:00",
    ],
];

$topReviews = $reviews;
usort($topReviews, function ($a, $b) {
    return ((float) $b["puntuacion"] <=> (float) $a["puntuacion"]);
});
$topReviews = array_slice($topReviews, 0, 3);

if ($reviewsReady) {
    $whereType = reviewTypeWhere($filter);

    $summarySql = "SELECT
            COUNT(*) AS total,
            COALESCE(AVG(puntuacion), 0) AS promedio,
            SUM(CASE WHEN puntuacion >= 8 THEN 1 ELSE 0 END) AS positivas,
            SUM(CASE WHEN puntuacion BETWEEN 5 AND 7 THEN 1 ELSE 0 END) AS intermedias,
            SUM(CASE WHEN puntuacion < 5 THEN 1 ELSE 0 END) AS malas,
            COALESCE(AVG(personal), 0) AS personal,
            COALESCE(AVG(instalaciones), 0) AS instalaciones,
            COALESCE(AVG(limpieza), 0) AS limpieza,
            COALESCE(AVG(confort), 0) AS confort,
            COALESCE(AVG(calidad_precio), 0) AS calidad_precio,
            COALESCE(AVG(ubicacion), 0) AS ubicacion,
            COALESCE(AVG(wifi), 0) AS wifi
        FROM resenas
        WHERE estado = 'publicado'";

    $summaryData = $conn->query($summarySql)->fetch_assoc();
    $total = (int) ($summaryData["total"] ?? 0);

    if ($total > 0) {
        $summary = [
            "total" => $total,
            "promedio" => round((float) $summaryData["promedio"], 1),
            "positivas" => (int) $summaryData["positivas"],
            "intermedias" => (int) $summaryData["intermedias"],
            "malas" => (int) $summaryData["malas"],
            "porcentaje" => round(((int) $summaryData["positivas"] / $total) * 100),
        ];

        $categories = [
            "Personal" => round((float) $summaryData["personal"], 1),
            "Instalaciones y servicios" => round((float) $summaryData["instalaciones"], 1),
            "Limpieza" => round((float) $summaryData["limpieza"], 1),
            "Confort" => round((float) $summaryData["confort"], 1),
            "Relación calidad-precio" => round((float) $summaryData["calidad_precio"], 1),
            "Ubicación" => round((float) $summaryData["ubicacion"], 1),
            "WiFi gratis" => round((float) $summaryData["wifi"], 1),
        ];
    }

    $reviewSql = "SELECT * FROM resenas WHERE estado = 'publicado' {$whereType} ORDER BY {$orderSql}";
    $reviewResult = $conn->query($reviewSql);
    $reviews = $reviewResult ? $reviewResult->fetch_all(MYSQLI_ASSOC) : [];

    $topSql = "SELECT * FROM resenas WHERE estado = 'publicado' ORDER BY puntuacion DESC, creado_en DESC LIMIT 3";
    $topResult = $conn->query($topSql);
    $topReviews = $topResult ? $topResult->fetch_all(MYSQLI_ASSOC) : [];
}

$flash = $_SESSION["review_flash"] ?? null;
unset($_SESSION["review_flash"]);
?>

<body>

<?php include("includes/navbar.php"); ?>

<section class="page-hero reviews-hero">
    <div class="container text-center">
        <p class="section-kicker">Basado en experiencias reales</p>
        <h1>Nuestros huéspedes opinan</h1>
        <p>La tranquilidad, la atención y la ubicación son parte de lo que más valoran quienes nos visitan en Tacna.</p>
    </div>
</section>

<main class="reviews-section py-5">
    <div class="container reviews-shell">
        <?php if ($flash): ?>
            <div class="alert alert-<?php echo htmlspecialchars($flash["type"]); ?> review-alert">
                <?php echo htmlspecialchars($flash["message"]); ?>
            </div>
        <?php endif; ?>

        <section class="rating-panel mx-auto mb-5">
            <span class="rating-number"><?php echo number_format((float) $summary["promedio"], 1); ?></span>
            <div>
                <h2><?php echo reviewLabel($summary["promedio"]); ?></h2>
                <p>Resumen de calificación de huéspedes</p>
                <small><?php echo (int) $summary["total"]; ?> comentario(s) registrados</small>
            </div>
        </section>

        <section class="review-metrics mb-5">
            <article>
                <span><?php echo (int) $summary["porcentaje"]; ?>%</span>
                <p>Opiniones positivas</p>
            </article>
            <article>
                <span><?php echo (int) $summary["positivas"]; ?></span>
                <p>Buenas</p>
            </article>
            <article>
                <span><?php echo (int) $summary["intermedias"]; ?></span>
                <p>Intermedias</p>
            </article>
            <article>
                <span><?php echo (int) $summary["malas"]; ?></span>
                <p>Por mejorar</p>
            </article>
        </section>

        <section class="featured-reviews mb-5">
            <div class="reviews-section-heading">
                <p class="section-kicker">Comentarios destacados</p>
                <h2>Los 3 mejores comentarios</h2>
            </div>

            <div class="featured-review-grid">
                <?php foreach ($topReviews as $review): ?>
                    <article class="review-card featured-review-card">
                        <div class="review-stars"><?php echo reviewStars($review["puntuacion"]); ?></div>
                        <p>"<?php echo htmlspecialchars($review["comentario"]); ?>"</p>
                        <div>
                            <h3><?php echo htmlspecialchars($review["nombre"]); ?></h3>
                            <span><?php echo htmlspecialchars($review["tipo_viaje"] ?: "Estadía"); ?></span>
                        </div>
                        <strong><?php echo number_format((float) $review["puntuacion"], 1); ?></strong>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="review-categories mb-5">
            <h2>Categorías</h2>
            <div class="review-category-grid">
                <?php foreach ($categories as $name => $score): ?>
                    <div class="review-category">
                        <div>
                            <strong><?php echo htmlspecialchars($name); ?></strong>
                            <span><?php echo number_format((float) $score, 1); ?></span>
                        </div>
                        <progress max="10" value="<?php echo htmlspecialchars((string) $score); ?>"></progress>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="trust-strip mb-5">
            <span>Fuentes de confianza</span>
            <strong>Booking.com</strong>
            <strong>Google</strong>
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#reviewModal">
                Déjanos tu opinión
            </button>
        </section>

        <section class="review-tools mb-4">
            <div class="review-filter-tabs">
                <a href="resenas.php?tipo=todas&orden=<?php echo urlencode($sort); ?>" class="<?php echo $filter === "todas" ? "active" : ""; ?>">Todas</a>
                <a href="resenas.php?tipo=buenas&orden=<?php echo urlencode($sort); ?>" class="<?php echo $filter === "buenas" ? "active" : ""; ?>">Buenas</a>
                <a href="resenas.php?tipo=intermedias&orden=<?php echo urlencode($sort); ?>" class="<?php echo $filter === "intermedias" ? "active" : ""; ?>">Intermedias</a>
                <a href="resenas.php?tipo=malas&orden=<?php echo urlencode($sort); ?>" class="<?php echo $filter === "malas" ? "active" : ""; ?>">Por mejorar</a>
            </div>

            <form method="GET">
                <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($filter); ?>">
                <label>
                    Ordenar comentarios por:
                    <select name="orden" onchange="this.form.submit()">
                        <option value="recientes" <?php echo $sort === "recientes" ? "selected" : ""; ?>>Más recientes primero</option>
                        <option value="mejor" <?php echo $sort === "mejor" ? "selected" : ""; ?>>Mejor puntuación</option>
                        <option value="menor" <?php echo $sort === "menor" ? "selected" : ""; ?>>Menor puntuación</option>
                    </select>
                </label>
            </form>
        </section>

        <section class="review-list">
            <h2>Comentarios de clientes</h2>

            <?php if (!$reviews): ?>
                <article class="review-empty">
                    <p>No hay comentarios para este filtro todavía.</p>
                </article>
            <?php endif; ?>

            <?php foreach ($reviews as $review): ?>
                <article class="review-row">
                    <aside>
                        <div class="review-avatar"><?php echo strtoupper(substr($review["nombre"], 0, 1)); ?></div>
                        <h3><?php echo htmlspecialchars($review["nombre"]); ?></h3>
                        <p><?php echo htmlspecialchars($review["pais"] ?: "Perú"); ?></p>
                        <span><i class="bi bi-door-open"></i> <?php echo htmlspecialchars($review["habitacion"] ?: "Habitación"); ?></span>
                        <span><i class="bi bi-calendar4-week"></i> <?php echo (int) ($review["noches"] ?: 1); ?> noche(s)</span>
                        <span><i class="bi bi-people"></i> <?php echo htmlspecialchars($review["tipo_viaje"] ?: "Estadía"); ?></span>
                    </aside>

                    <div class="review-content">
                        <small>Fecha del comentario: <?php echo date("d/m/Y", strtotime($review["creado_en"])); ?></small>
                        <h3><?php echo htmlspecialchars($review["titulo"] ?: reviewLabel($review["puntuacion"])); ?></h3>
                        <div class="review-stars"><?php echo reviewStars($review["puntuacion"]); ?></div>
                        <p><?php echo htmlspecialchars($review["comentario"]); ?></p>
                        <p class="review-good"><i class="bi bi-emoji-smile"></i> <?php echo htmlspecialchars($review["lo_mejor"] ?: "Buena experiencia general"); ?></p>
                        <p class="review-bad"><i class="bi bi-emoji-neutral"></i> <?php echo htmlspecialchars($review["mejorar"] ?: "Sin comentarios"); ?></p>
                    </div>

                    <strong class="review-score"><?php echo number_format((float) $review["puntuacion"], 1); ?></strong>
                </article>
            <?php endforeach; ?>
        </section>
    </div>
</main>

<div class="modal fade review-modal" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" action="guardar_resena.php" method="POST">
            <div class="modal-header">
                <div>
                    <p class="section-kicker mb-1">Tu experiencia importa</p>
                    <h2 class="modal-title">Déjanos tu opinión</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <?php if (!$reviewsReady): ?>
                    <div class="alert alert-warning">
                        Primero ejecuta el SQL de reseñas en phpMyAdmin para guardar opiniones reales.
                    </div>
                <?php endif; ?>

                <div class="review-form-grid">
                    <label>Nombre
                        <input type="text" name="nombre" required maxlength="120" value="<?php echo htmlspecialchars($_SESSION["usuario"] ?? ""); ?>">
                    </label>
                    <label>Correo
                        <input type="email" name="correo" maxlength="120">
                    </label>
                    <label>País
                        <input type="text" name="pais" maxlength="80" value="Perú">
                    </label>
                    <label>Tipo de viaje
                        <select name="tipo_viaje">
                            <option>Viaje familiar</option>
                            <option>Estadía de trabajo</option>
                            <option>Fin de semana</option>
                            <option>En pareja</option>
                            <option>Otro</option>
                        </select>
                    </label>
                    <label>Habitación
                        <input type="text" name="habitacion" maxlength="120" placeholder="Ej. Habitación Doble">
                    </label>
                    <label>Noches
                        <input type="number" name="noches" min="1" max="60" value="1">
                    </label>
                    <label>Puntuación general
                        <select name="puntuacion" required>
                            <?php for ($i = 10; $i >= 1; $i--): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?>/10</option>
                            <?php endfor; ?>
                        </select>
                    </label>
                    <label>Título
                        <input type="text" name="titulo" maxlength="120" placeholder="Ej. Muy bueno">
                    </label>
                </div>

                <div class="review-form-grid categories">
                    <?php foreach (["personal" => "Personal", "instalaciones" => "Instalaciones", "limpieza" => "Limpieza", "confort" => "Confort", "calidad_precio" => "Calidad-precio", "ubicacion" => "Ubicación", "wifi" => "WiFi"] as $field => $label): ?>
                        <label><?php echo $label; ?>
                            <input type="number" name="<?php echo $field; ?>" min="1" max="10" value="8">
                        </label>
                    <?php endforeach; ?>
                </div>

                <label class="review-full-label">Comentario
                    <textarea name="comentario" rows="4" required maxlength="900" placeholder="Cuéntanos cómo fue tu experiencia"></textarea>
                </label>
                <label class="review-full-label">Lo mejor de tu estadía
                    <input type="text" name="lo_mejor" maxlength="180" placeholder="Ej. La ubicación, la atención, el desayuno">
                </label>
                <label class="review-full-label">Qué podríamos mejorar
                    <input type="text" name="mejorar" maxlength="180" placeholder="Opcional">
                </label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-warning" <?php echo !$reviewsReady ? "disabled" : ""; ?>>Enviar opinión</button>
            </div>
        </form>
    </div>
</div>

<?php include("includes/footer.php"); ?>

</body>

</html>
