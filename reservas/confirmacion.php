<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");
include("../config/db_utils.php");

$reservaId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$reserva = $_SESSION["ultima_reserva"] ?? null;

if (!$reserva || (int) ($reserva["id"] ?? 0) !== $reservaId) {
    $sql = "SELECT reservas.*, habitaciones.nombre AS habitacion, habitaciones.precio
            FROM reservas
            INNER JOIN habitaciones ON reservas.habitacion_id = habitaciones.id
            WHERE reservas.id = ? AND reservas.usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $reservaId, $_SESSION["id_usuario"]);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();

    if (!$fila) {
        die("Reserva no encontrada.");
    }

    $entrada = new DateTime($fila["fecha_ingreso"]);
    $salida = new DateTime($fila["fecha_salida"]);
    $noches = max(1, (int) $entrada->diff($salida)->days);

    $tieneTotales = db_column_exists($conn, "reservas", "total") && db_column_exists($conn, "reservas", "metodo_pago");

    $reserva = [
        "id" => $fila["id"],
        "codigo" => $fila["codigo"] ?? ("MST" . str_pad((string) $fila["id"], 6, "0", STR_PAD_LEFT)),
        "cliente" => $_SESSION["usuario"] ?? "Cliente",
        "email" => $_SESSION["correo"] ?? "",
        "habitacion" => $fila["habitacion"],
        "fecha_ingreso" => $fila["fecha_ingreso"],
        "fecha_salida" => $fila["fecha_salida"],
        "adultos" => $fila["adultos"],
        "ninos" => $fila["ninos"],
        "noches" => isset($fila["noches"]) ? (int) $fila["noches"] : $noches,
        "precio_noche" => isset($fila["precio_noche"]) && (float) $fila["precio_noche"] > 0 ? $fila["precio_noche"] : $fila["precio"],
        "total" => $tieneTotales && (float) ($fila["total"] ?? 0) > 0 ? $fila["total"] : $noches * (float) $fila["precio"],
        "metodo_pago" => $tieneTotales ? ($fila["metodo_pago"] ?: "Pendiente") : "Pendiente",
        "estado" => ucfirst($fila["estado"]),
    ];
}

include("../includes/header.php");
?>

<body>

<?php include("../includes/navbar.php"); ?>

<style>
@media print {
    *{-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important}
    html,body{background:white!important;margin:0!important;padding:0!important}
    body *{visibility:hidden!important}
    .confirmation-page,.confirmation-page *{visibility:visible!important}
    nav,footer,.navbar,.confirmation-actions{display:none!important}
    .confirmation-page{align-items:center!important;background:white!important;display:flex!important;justify-content:center!important;min-height:auto!important;padding:0!important;position:absolute!important;inset:0!important;width:100%!important}
    .confirmation-card{background:#050505!important;border:8px solid #c7962a!important;box-shadow:none!important;color:white!important;margin:0 auto!important;max-width:150mm!important;padding:20mm 16mm!important;page-break-after:avoid!important;page-break-before:avoid!important;page-break-inside:avoid!important;width:150mm!important}
    .confirmation-box{border:1px solid #c7962a!important;color:white!important}
    .confirmation-box h1,.next-steps h2,.reservation-code b,.next-steps i{color:#d8b233!important}
    .confirmation-box dt,.confirmation-box dd,.next-steps p{color:white!important}
    .confirmation-box span{background:#e0b83a!important;color:#111!important}
    @page{size:A4 portrait;margin:10mm}
}
</style>

<main class="confirmation-page">
    <section class="confirmation-card">
        <div class="confirmation-mark">
            <img src="../assets/img/logo.png" alt="Madison Suite">
            <i class="bi bi-check-circle-fill"></i>
        </div>

        <div class="confirmation-box">
            <h1>Detalles de la Reserva</h1>
            <p class="reservation-code">Número de Reserva: <b>#<?php echo htmlspecialchars($reserva["codigo"]); ?></b></p>

            <dl>
                <div><dt>Nombre del Cliente:</dt><dd><?php echo htmlspecialchars($reserva["cliente"]); ?></dd></div>
                <div><dt>Fecha de Estancia:</dt><dd><?php echo htmlspecialchars($reserva["fecha_ingreso"]); ?> al <?php echo htmlspecialchars($reserva["fecha_salida"]); ?></dd></div>
                <div><dt>Tipo de Habitación:</dt><dd><?php echo htmlspecialchars($reserva["habitacion"]); ?></dd></div>
                <div><dt>Número de Personas:</dt><dd><?php echo (int) $reserva["adultos"]; ?> adulto(s), <?php echo (int) $reserva["ninos"]; ?> niño(s)</dd></div>
                <div><dt>Noches:</dt><dd><?php echo (int) $reserva["noches"]; ?></dd></div>
                <div><dt>Total:</dt><dd>S/ <?php echo number_format((float) $reserva["total"], 0); ?></dd></div>
                <div><dt>Medio de Pago:</dt><dd><?php echo htmlspecialchars($reserva["metodo_pago"]); ?></dd></div>
                <div><dt>Estado:</dt><dd><span><?php echo htmlspecialchars($reserva["estado"]); ?></span></dd></div>
            </dl>
        </div>

        <div class="next-steps">
            <h2>Pasos Siguientes</h2>
            <i class="bi bi-star"></i>
            <p>Hemos registrado tu reserva y enviado el resumen a <?php echo htmlspecialchars($reserva["email"] ?: "tu correo registrado"); ?>. Para confirmar el pago, envía tu comprobante por WhatsApp.</p>
            <p>Recuerda que el check-in es desde las 13:00 y el check-out es hasta las 11:30.</p>
        </div>

        <div class="confirmation-actions">
            <button type="button" onclick="window.print()">Descargar Confirmación en PDF</button>
            <a href="mis_reservas.php">Ver Mis Reservas</a>
        </div>
    </section>
</main>

<?php include("../includes/footer.php"); ?>

</body>
</html>
