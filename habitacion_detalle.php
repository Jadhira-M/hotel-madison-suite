<?php
session_start();
include("includes/header.php");
include("config/conexion.php");

function roomImagePath($imagen)
{
    $imagen = trim((string) $imagen);
    if ($imagen === "") {
        return "assets/img/habitaciones/doble.jpg";
    }
    if (strpos($imagen, "assets/") === 0) {
        return is_file(__DIR__ . "/" . $imagen) ? $imagen : "assets/img/habitaciones/doble.jpg";
    }
    $path = "assets/img/habitaciones/" . $imagen;
    return is_file(__DIR__ . "/" . $path) ? $path : "assets/img/habitaciones/doble.jpg";
}

function roomDetails($habitacion)
{
    $details = [];
    $camas = (int) ($habitacion["camas"] ?? 0);
    $capacidad = (int) ($habitacion["capacidad"] ?? 0);
    $nombre = trim((string) ($habitacion["nombre"] ?? ""));

    if (stripos($nombre, "Triple") !== false) {
        $details[] = "3 camas: 1 plaza y 2 camas individuales";
    } elseif ($camas > 0) {
        $details[] = $camas === 1 ? "1 cama disponible" : $camas . " camas disponibles";
    }

    if ($capacidad > 0) {
        $details[] = "Capacidad máxima: " . $capacidad . " personas";
    }

    $details[] = "Desayuno tipo buffet";
    $details[] = "Baño privado";
    $details[] = "Agua caliente";
    $details[] = "Internet WiFi";
    $details[] = "Cable TV";
    $details[] = "Amplia cochera";

    return $details;
}

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

function formatRoomDate($date)
{
    return date("d/m/Y", strtotime($date));
}

function roomAvailability($conn, $habitacion)
{
    $habitacionId = (int) ($habitacion["id"] ?? 0);
    $manualStatus = strtolower(trim((string) ($habitacion["estado"] ?? "disponible")));

    $availability = [
        "class" => "is-free",
        "message" => "Disponible para reservar",
    ];

    if ($habitacionId <= 0) {
        return $availability;
    }

    $stmt = $conn->prepare("
        SELECT fecha_ingreso, fecha_salida
        FROM reservas
        WHERE habitacion_id = ?
          AND estado <> 'cancelada'
          AND fecha_salida >= CURDATE()
        ORDER BY fecha_ingreso ASC
        LIMIT 1
    ");

    if (!$stmt) {
        return $availability;
    }

    $stmt->bind_param("i", $habitacionId);
    $stmt->execute();
    $reserva = $stmt->get_result()->fetch_assoc();

    if ($reserva) {
        $hoy = date("Y-m-d");
        $desde = $reserva["fecha_ingreso"];
        $hasta = $reserva["fecha_salida"];

        if ($hoy >= $desde && $hoy < $hasta) {
            return [
                "class" => "is-busy",
                "message" => "Ocupada del " . formatRoomDate($desde) . " al " . formatRoomDate($hasta) . ". Disponible desde el " . formatRoomDate($hasta) . ".",
            ];
        }

        return [
            "class" => "is-upcoming",
            "message" => "Disponible ahora. PrÃ³xima ocupaciÃ³n: " . formatRoomDate($desde) . " al " . formatRoomDate($hasta) . ".",
        ];
    }

    if ($manualStatus !== "" && $manualStatus !== "disponible") {
        return [
            "class" => "is-upcoming",
            "message" => "Marcada como ocupada en administraciÃ³n. Puedes consultar fechas al reservar.",
        ];
    }

    return $availability;
}

$id = (int) ($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT * FROM habitaciones WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$habitacion = $stmt->get_result()->fetch_assoc();

if (!$habitacion) {
    header("Location: habitaciones.php");
    exit();
}

$mainImage = roomImagePath($habitacion["imagen"] ?? "");
$galeria = array_values(array_unique([
    $mainImage,
]));

if (tableExists($conn, "habitacion_imagenes")) {
    $stmtGallery = $conn->prepare("SELECT imagen FROM habitacion_imagenes WHERE habitacion_id = ? ORDER BY orden ASC, id ASC");
    $stmtGallery->bind_param("i", $id);
    $stmtGallery->execute();
    $galleryResult = $stmtGallery->get_result();

    while ($image = $galleryResult->fetch_assoc()) {
        $galeria[] = roomImagePath($image["imagen"]);
    }

    $galeria = array_values(array_unique($galeria));
}

$reservaUrl = "reservas/reservar.php?id=" . (int) $habitacion["id"];
$loginUrl = "auth/login.php?redirect=../" . urlencode($reservaUrl);
?>

<body>

<?php include("includes/navbar.php"); ?>

<main class="room-detail-page">

<section class="container room-detail-shell">

<h1><?php echo htmlspecialchars($habitacion["nombre"]); ?></h1>

<div class="room-detail-gallery">

<?php foreach ($galeria as $imagen) { ?>

<img src="<?php echo htmlspecialchars($imagen); ?>"
alt="<?php echo htmlspecialchars($habitacion["nombre"]); ?>">

<?php } ?>

</div>

<div class="room-key-card">

<h2>Detalles clave</h2>

<div>
<span><i class="bi bi-house-door"></i> <?php echo (int) $habitacion["camas"]; ?> cama(s)</span>
<span><i class="bi bi-tag"></i> <?php echo htmlspecialchars($habitacion["tipo"]); ?></span>
<span><i class="bi bi-people"></i> Capacidad mÃ¡x: <?php echo (int) $habitacion["capacidad"]; ?> personas</span>
</div>

</div>

<div class="room-detail-grid">

<article class="room-detail-panel">

<h2>Detalles de la habitaciÃ³n</h2>

<h3><?php echo htmlspecialchars($habitacion["nombre"]); ?></h3>

<p><?php echo htmlspecialchars(cleanRoomDescription($habitacion["descripcion"])); ?></p>

<ul>

<?php foreach (roomDetails($habitacion) as $detalle) { ?>

<li><i class="bi bi-check2-square"></i> <?php echo htmlspecialchars($detalle); ?></li>

<?php } ?>

</ul>

</article>

<aside class="room-reservation-panel">

<h2>Opciones de reserva</h2>

<div class="room-price">
S/ <?php echo number_format((float) $habitacion["precio"], 0); ?>
<span>Incluye IGV</span>
</div>

<ul>
<li><i class="bi bi-check2"></i> Desayuno tipo buffet incluido.</li>
<li><i class="bi bi-check2"></i> Baño privado.</li>
<li><i class="bi bi-check2"></i> Agua caliente.</li>
<li><i class="bi bi-check2"></i> Internet WiFi gratis.</li>
<li><i class="bi bi-check2"></i> Cable TV.</li>
<li><i class="bi bi-check2"></i> Amplia cochera.</li>
<li><i class="bi bi-check2"></i> Parcialmente reembolsable.</li>
<li><i class="bi bi-check2"></i> Pagas al alojamiento antes de llegar.</li>
</ul>

<a href="<?php echo isset($_SESSION["usuario"]) ? $reservaUrl : $loginUrl; ?>"
class="btn btn-warning w-100">
Reservar ahora
</a>

</aside>

</div>

</section>

</main>

<?php include("includes/footer.php"); ?>

</body>

</html>
