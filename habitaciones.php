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
        $details[] = $camas === 1 ? "1 cama" : $camas . " camas";
    }

    $details[] = "Desayuno tipo buffet";
    $details[] = "Baño privado";
    $details[] = "Agua caliente";
    $details[] = "Internet WiFi";
    $details[] = "Cable TV";
    $details[] = "Amplia cochera";

    if ($capacidad > 0) {
        $details[] = "Capacidad máxima: " . $capacidad . " personas";
    }

    return array_slice($details, 0, 4);
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

$sql = "SELECT * FROM habitaciones ORDER BY numero ASC, id ASC";
$resultado = $conn->query($sql);
$habitaciones = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
?>

<body>

<?php include("includes/navbar.php"); ?>

<main class="rooms-page">

<section class="container rooms-shell">

<div class="rooms-heading">

<h1>Elegancia y descanso</h1>

<p>
Encuentra el espacio perfecto para tu estadÃ­a en Tacna.
</p>

</div>

<?php if (!$habitaciones): ?>
    <div class="alert alert-warning">
        TodavÃ­a no hay habitaciones registradas.
    </div>
<?php endif; ?>

<div class="fixed-room-grid">

<?php foreach ($habitaciones as $habitacion) { ?>

<?php
$reservaUrl = "reservas/reservar.php?id=" . (int) $habitacion["id"];
$loginUrl = "auth/login.php?redirect=../" . urlencode($reservaUrl);
?>

<article class="fixed-room-card">

<a href="habitacion_detalle.php?id=<?php echo (int) $habitacion["id"]; ?>"
class="fixed-room-image-link">

<img src="<?php echo htmlspecialchars(roomImagePath($habitacion["imagen"] ?? "")); ?>"
alt="<?php echo htmlspecialchars($habitacion["nombre"]); ?>">

</a>

<div class="fixed-room-title">

<h2>
<a href="habitacion_detalle.php?id=<?php echo (int) $habitacion["id"]; ?>">
<?php echo htmlspecialchars($habitacion["nombre"]); ?>
</a>
</h2>

<span>
<i class="bi bi-people-fill"></i>
<?php echo (int) $habitacion["capacidad"]; ?>
</span>

</div>

<ul>

<?php foreach (roomDetails($habitacion) as $detalle) { ?>

<li>
<i class="bi bi-check2-square"></i>
<?php echo htmlspecialchars($detalle); ?>
</li>

<?php } ?>

</ul>

<div class="fixed-room-action">

<a href="habitacion_detalle.php?id=<?php echo (int) $habitacion["id"]; ?>"
class="room-detail-link">
Ver detalles
</a>

<a href="<?php echo isset($_SESSION["usuario"]) ? $reservaUrl : $loginUrl; ?>"
class="btn btn-warning">
Reservar
</a>

<strong>S/ <?php echo number_format((float) $habitacion["precio"], 0); ?></strong>

</div>

</article>

<?php } ?>

</div>

</section>

</main>

<?php include("includes/footer.php"); ?>

</body>

</html>
