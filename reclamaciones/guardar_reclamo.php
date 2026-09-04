<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: libro.php");
    exit();
}

include("../config/conexion.php");
include("../config/db_utils.php");

$fecha = date("Y-m-d");
$hora = date("H:i");
$tipoIncidencia = trim($_POST["tipo_incidencia"] ?? "Reclamo");
$nombre = trim($_POST["nombre"] ?? "");
$documento = trim($_POST["documento"] ?? "");
$domicilio = trim($_POST["domicilio"] ?? "");
$correo = trim($_POST["correo"] ?? "");
$telefono = trim($_POST["telefono"] ?? "");
$tipoBien = trim($_POST["tipo_bien"] ?? "Servicio");
$monto = (float) ($_POST["monto"] ?? 0);
$bienServicio = trim($_POST["bien_servicio"] ?? "");
$detalle = trim($_POST["detalle"] ?? "");
$pedido = trim($_POST["pedido"] ?? "");
$estado = "pendiente";
$prioridad = "Media";
$usuarioId = isset($_SESSION["id_usuario"]) ? (int) $_SESSION["id_usuario"] : null;
$tipo = $tipoIncidencia;

if ($nombre === "" || $documento === "" || $correo === "" || $detalle === "" || $pedido === "") {
    die("Completa los datos obligatorios del reclamo.");
}

$usaTablaMejorada = db_column_exists($conn, "reclamaciones", "codigo")
    && db_column_exists($conn, "reclamaciones", "nombre")
    && db_column_exists($conn, "reclamaciones", "documento")
    && db_column_exists($conn, "reclamaciones", "pedido");

if ($usaTablaMejorada) {
    $sql = "INSERT INTO reclamaciones
            (usuario_id, codigo, nombre, documento, domicilio, correo, telefono, tipo, tipo_bien, bien_servicio, monto, detalle, pedido, prioridad, fecha, estado)
            VALUES (?, '', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "issssssssdsssss",
        $usuarioId,
        $nombre,
        $documento,
        $domicilio,
        $correo,
        $telefono,
        $tipo,
        $tipoBien,
        $bienServicio,
        $monto,
        $detalle,
        $pedido,
        $prioridad,
        $fecha,
        $estado
    );
} else {
    $detalleCompleto = $detalle . "\n\nPedido del consumidor: " . $pedido;
    $sql = "INSERT INTO reclamaciones (usuario_id, tipo, detalle, fecha, estado) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $usuarioId, $tipo, $detalleCompleto, $fecha, $estado);
}

if (!$stmt->execute()) {
    die("No se pudo registrar el reclamo.");
}

$reclamoId = $stmt->insert_id;
$codigo = "REC-" . str_pad((string) $reclamoId, 3, "0", STR_PAD_LEFT);

if ($usaTablaMejorada) {
    $stmtCodigo = $conn->prepare("UPDATE reclamaciones SET codigo = ? WHERE id = ?");
    $stmtCodigo->bind_param("si", $codigo, $reclamoId);
    $stmtCodigo->execute();
}

$reclamo = [
    "id" => $codigo,
    "codigo" => $codigo,
    "titulo" => $tipoIncidencia . " - " . ($bienServicio ?: "Servicio del hotel"),
    "fecha" => $fecha,
    "hora" => $hora,
    "habitacion" => "-",
    "prioridad" => $prioridad,
    "huesped" => $nombre,
    "tipo" => $tipoIncidencia,
    "descripcion" => $detalle,
    "estado" => $estado,
    "detalle" => "Pedido del consumidor: " . $pedido,
    "documento" => $documento,
    "domicilio" => $domicilio,
    "correo" => $correo,
    "telefono" => $telefono,
    "tipo_bien" => $tipoBien,
    "monto" => $monto,
    "bien_servicio" => $bienServicio,
    "pedido" => $pedido,
];

$_SESSION["ultimo_reclamo"] = $reclamo;

header("Location: confirmar_reclamo.php?codigo=" . urlencode($codigo));
exit();
