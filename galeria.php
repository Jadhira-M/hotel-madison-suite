<?php
session_start();
include("includes/header.php");
include("config/conexion.php");

function galleryTableExists($conn)
{
    $database = $conn->query("SELECT DATABASE() AS db")->fetch_assoc()["db"] ?? "";
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = ?
          AND TABLE_NAME = 'galeria'
    ");
    $stmt->bind_param("s", $database);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return (int) ($row["total"] ?? 0) > 0;
}

$imagenes = [
    ["src" => "assets/img/hotel.jpg", "titulo" => "Fachada Madison Suite"],
    ["src" => "assets/img/servicios/desayuno.jpg", "titulo" => "Desayuno"],
    ["src" => "assets/img/servicios/restaurante.jpg", "titulo" => "Restaurante"],
    ["src" => "assets/img/recepcion.jpg", "titulo" => "Recepción"],
    ["src" => "assets/img/habitaciones/doble.jpg", "titulo" => "Habitación doble"],
    ["src" => "assets/img/habitaciones/familiar plus.jpeg", "titulo" => "Habitación familiar"],
    ["src" => "assets/img/servicios/estacionamiento.jpg", "titulo" => "Cochera"],
    ["src" => "assets/img/servicios/piscina.jpg", "titulo" => "Ambientes del hotel"],
];

if (galleryTableExists($conn)) {
    $resultado = $conn->query("SELECT titulo, imagen FROM galeria WHERE estado = 'activo' ORDER BY orden ASC, id ASC");
    $filas = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];

    if ($filas) {
        $imagenes = array_map(function ($fila) {
            return [
                "src" => $fila["imagen"],
                "titulo" => $fila["titulo"],
            ];
        }, $filas);
    }
}
?>

<body>

<?php include("includes/navbar.php"); ?>

<main class="gallery-page">

<section class="container gallery-shell">

<h1>Nuestras galerías de fotos</h1>

<div id="madisonGallery" class="carousel slide gallery-carousel" data-bs-ride="carousel">

<div class="carousel-inner">

<?php foreach ($imagenes as $index => $imagen) { ?>

<div class="carousel-item <?php echo $index === 0 ? "active" : ""; ?>">

<img src="<?php echo htmlspecialchars($imagen["src"]); ?>"
class="d-block w-100"
alt="<?php echo htmlspecialchars($imagen["titulo"]); ?>">

<div class="gallery-caption">
<?php echo htmlspecialchars($imagen["titulo"]); ?>
</div>

</div>

<?php } ?>

</div>

<button class="carousel-control-prev" type="button" data-bs-target="#madisonGallery" data-bs-slide="prev">
<span class="carousel-control-prev-icon" aria-hidden="true"></span>
<span class="visually-hidden">Anterior</span>
</button>

<button class="carousel-control-next" type="button" data-bs-target="#madisonGallery" data-bs-slide="next">
<span class="carousel-control-next-icon" aria-hidden="true"></span>
<span class="visually-hidden">Siguiente</span>
</button>

</div>

<div class="gallery-thumbnails">

<?php foreach ($imagenes as $index => $imagen) { ?>

<button type="button"
data-bs-target="#madisonGallery"
data-bs-slide-to="<?php echo $index; ?>"
class="<?php echo $index === 0 ? "active" : ""; ?>"
aria-label="Ver <?php echo htmlspecialchars($imagen["titulo"]); ?>">

<img src="<?php echo htmlspecialchars($imagen["src"]); ?>"
alt="<?php echo htmlspecialchars($imagen["titulo"]); ?>">

</button>

<?php } ?>

</div>

</section>

</main>

<?php include("includes/footer.php"); ?>

</body>

</html>
