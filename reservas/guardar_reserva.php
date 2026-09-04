<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");
include("../config/db_utils.php");

// Obtener el ID del usuario que inició sesión
$usuario_id = $_SESSION['id_usuario'];

// Obtener datos del formulario
$habitacion_id = (int) ($_POST['habitacion_id'] ?? 0);
$fecha_ingreso = $_POST['fecha_ingreso'];
$fecha_salida = $_POST['fecha_salida'];
$adultos = $_POST['adultos'];
$ninos = $_POST['ninos'];
$personas = $adultos + $ninos;
$metodo_pago = trim($_POST['metodo_pago'] ?? 'Pendiente');
$noches = max(1, (int) ($_POST['noches'] ?? 1));
$total = max(0, (float) ($_POST['total'] ?? 0));

// Validaciones
if ($fecha_ingreso >= $fecha_salida) {
    die("La fecha de salida debe ser posterior a la fecha de ingreso.");
}

$hoy = date("Y-m-d");

if ($fecha_ingreso < $hoy) {
    die("La fecha de ingreso no puede ser anterior a hoy.");
}

// Consultar capacidad de la habitación
$sqlHabitacion = "SELECT id, nombre, precio, capacidad FROM habitaciones WHERE id = ?";
$stmtHabitacion = $conn->prepare($sqlHabitacion);
$stmtHabitacion->bind_param("i", $habitacion_id);
$stmtHabitacion->execute();

$resultadoHabitacion = $stmtHabitacion->get_result();
$habitacion = $resultadoHabitacion->fetch_assoc();

if (!$habitacion) {
    die("Habitacion no encontrada en la base de datos. Vuelve a Habitaciones y selecciona una habitacion disponible.");
}

if ($personas > $habitacion['capacidad']) {
    die("La cantidad de personas supera la capacidad de la habitación.");
}

$stmtCruce = $conn->prepare("
    SELECT fecha_ingreso, fecha_salida
    FROM reservas
    WHERE habitacion_id = ?
      AND estado <> 'cancelada'
      AND fecha_ingreso < ?
      AND fecha_salida > ?
    ORDER BY fecha_ingreso ASC
    LIMIT 1
");
$stmtCruce->bind_param("iss", $habitacion_id, $fecha_salida, $fecha_ingreso);
$stmtCruce->execute();
$reservaCruzada = $stmtCruce->get_result()->fetch_assoc();

if ($reservaCruzada) {
    $disponibleDesde = date("d/m/Y", strtotime($reservaCruzada["fecha_salida"]));
    die("La habitacion ya esta ocupada en esas fechas. Estara disponible desde el " . $disponibleDesde . ".");
}

$precioNoche = (float) ($habitacion["precio"] ?? 0);
if (!$total && $precioNoche > 0) {
    $total = $precioNoche * $noches;
}

$codigoReserva = null;
$tieneColumnasReserva = db_column_exists($conn, "reservas", "codigo")
    && db_column_exists($conn, "reservas", "noches")
    && db_column_exists($conn, "reservas", "precio_noche")
    && db_column_exists($conn, "reservas", "total")
    && db_column_exists($conn, "reservas", "metodo_pago");

if ($tieneColumnasReserva) {
    $sql = "INSERT INTO reservas
            (usuario_id, habitacion_id, fecha_ingreso, fecha_salida, adultos, ninos, personas, noches, precio_noche, total, metodo_pago, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iissiiiidds",
        $usuario_id,
        $habitacion_id,
        $fecha_ingreso,
        $fecha_salida,
        $adultos,
        $ninos,
        $personas,
        $noches,
        $precioNoche,
        $total,
        $metodo_pago
    );
} else {
    $sql = "INSERT INTO reservas
            (usuario_id, habitacion_id, fecha_ingreso, fecha_salida, adultos, ninos, personas, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iissiii",
        $usuario_id,
        $habitacion_id,
        $fecha_ingreso,
        $fecha_salida,
        $adultos,
        $ninos,
        $personas
    );
}

if ($stmt->execute()) {

    $reserva_id = $stmt->insert_id;
    $codigoReserva = "MST" . str_pad((string) $reserva_id, 6, "0", STR_PAD_LEFT);

    if ($tieneColumnasReserva) {
        $stmtCodigo = $conn->prepare("UPDATE reservas SET codigo = ? WHERE id = ?");
        $stmtCodigo->bind_param("si", $codigoReserva, $reserva_id);
        $stmtCodigo->execute();
    }

    if (db_table_exists($conn, "reserva_pagos")) {
        $estadoPago = "pendiente";
        $stmtPago = $conn->prepare("INSERT INTO reserva_pagos (reserva_id, metodo, monto, estado) VALUES (?, ?, ?, ?)");
        $stmtPago->bind_param("isds", $reserva_id, $metodo_pago, $total, $estadoPago);
        $stmtPago->execute();
    }

    $_SESSION["ultima_reserva"] = [
        "id" => $reserva_id,
        "codigo" => $codigoReserva,
        "cliente" => trim(($_POST["nombre"] ?? $_SESSION["usuario"]) . " " . ($_POST["apellido"] ?? "")),
        "email" => $_POST["email"] ?? ($_SESSION["correo"] ?? ""),
        "habitacion" => $habitacion["nombre"] ?? "Habitación",
        "fecha_ingreso" => $fecha_ingreso,
        "fecha_salida" => $fecha_salida,
        "adultos" => $adultos,
        "ninos" => $ninos,
        "noches" => $noches,
        "precio_noche" => $precioNoche,
        "total" => $total,
        "metodo_pago" => $metodo_pago,
        "estado" => "Pendiente",
    ];

    header("Location: confirmacion.php?id=" . $reserva_id);
    exit();

} else {

    echo "Error al guardar la reserva.";

}
?>
