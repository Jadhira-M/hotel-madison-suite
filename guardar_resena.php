<?php
session_start();
include("config/conexion.php");

function redirectReview($type, $message)
{
    $_SESSION["review_flash"] = [
        "type" => $type,
        "message" => $message,
    ];
    header("Location: resenas.php");
    exit;
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

function postText($key, $default = "")
{
    return trim($_POST[$key] ?? $default);
}

function postScore($key, $default = 8)
{
    $value = (int) ($_POST[$key] ?? $default);
    return max(1, min(10, $value));
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: resenas.php");
    exit;
}

if (!tableExists($conn, "resenas")) {
    redirectReview("warning", "Primero ejecuta el SQL de reseñas en phpMyAdmin para poder guardar opiniones.");
}

$nombre = postText("nombre");
$correo = postText("correo");
$pais = postText("pais", "Perú");
$tipoViaje = postText("tipo_viaje", "Estadía");
$habitacion = postText("habitacion");
$noches = max(1, min(60, (int) ($_POST["noches"] ?? 1)));
$puntuacion = postScore("puntuacion", 10);
$titulo = postText("titulo");
$comentario = postText("comentario");
$loMejor = postText("lo_mejor");
$mejorar = postText("mejorar");
$personal = postScore("personal");
$instalaciones = postScore("instalaciones");
$limpieza = postScore("limpieza");
$confort = postScore("confort");
$calidadPrecio = postScore("calidad_precio");
$ubicacion = postScore("ubicacion");
$wifi = postScore("wifi");
$usuarioId = isset($_SESSION["id_usuario"]) ? (int) $_SESSION["id_usuario"] : null;
$estado = "publicado";

if ($nombre === "" || $comentario === "") {
    redirectReview("danger", "Completa tu nombre y comentario para registrar la reseña.");
}

if ($titulo === "") {
    if ($puntuacion >= 8) {
        $titulo = "Muy bueno";
    } elseif ($puntuacion >= 5) {
        $titulo = "Normal";
    } else {
        $titulo = "Por mejorar";
    }
}

$sql = "INSERT INTO resenas
    (usuario_id, nombre, correo, pais, tipo_viaje, habitacion, noches, puntuacion, titulo, comentario, lo_mejor, mejorar, personal, instalaciones, limpieza, confort, calidad_precio, ubicacion, wifi, estado)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "isssssiissssiiiiiiis",
    $usuarioId,
    $nombre,
    $correo,
    $pais,
    $tipoViaje,
    $habitacion,
    $noches,
    $puntuacion,
    $titulo,
    $comentario,
    $loMejor,
    $mejorar,
    $personal,
    $instalaciones,
    $limpieza,
    $confort,
    $calidadPrecio,
    $ubicacion,
    $wifi,
    $estado
);

if ($stmt->execute()) {
    redirectReview("success", "Gracias por dejar tu opinión. Tu comentario ya aparece en reseñas.");
}

redirectReview("danger", "No se pudo guardar la reseña. Inténtalo nuevamente.");
?>
