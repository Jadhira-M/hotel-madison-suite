<?php
session_start();

$codigo = $_GET["codigo"] ?? "";
$reclamo = $_SESSION["ultimo_reclamo"] ?? null;

if (!$reclamo || (($reclamo["id"] ?? "") !== $codigo && ($reclamo["codigo"] ?? "") !== $codigo)) {
    include("../config/conexion.php");
    include("../config/db_utils.php");

    if ($codigo !== "" && db_table_exists($conn, "reclamaciones")) {
        if (db_column_exists($conn, "reclamaciones", "codigo")) {
            $stmt = $conn->prepare("SELECT * FROM reclamaciones WHERE codigo = ? LIMIT 1");
            $stmt->bind_param("s", $codigo);
        } else {
            $id = (int) preg_replace("/\D+/", "", $codigo);
            $stmt = $conn->prepare("SELECT * FROM reclamaciones WHERE id = ? LIMIT 1");
            $stmt->bind_param("i", $id);
        }

        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if ($row) {
            $reclamo = [
                "id" => $row["codigo"] ?? ("REC-" . str_pad((string) $row["id"], 3, "0", STR_PAD_LEFT)),
                "codigo" => $row["codigo"] ?? "",
                "fecha" => $row["fecha"] ?? "",
                "hora" => "",
                "tipo" => $row["tipo"] ?? "Reclamo",
                "descripcion" => $row["detalle"] ?? "",
                "estado" => $row["estado"] ?? "pendiente",
                "pedido" => $row["pedido"] ?? "",
                "correo" => $row["correo"] ?? "",
            ];
        }
    }
}

if (!$reclamo) {
    header("Location: libro.php");
    exit();
}

include("../includes/header.php");
?>

<body>

<?php include("../includes/navbar.php"); ?>

<main class="claim-confirm-page">
    <section class="claim-confirm-hero">
        <h1>Su Reclamo Ha Sido Recibido Correctamente</h1>
        <p>En cumplimiento con las regulaciones de protección al consumidor, hemos procesado su solicitud de reclamación. Su caso será revisado con prontitud.</p>
    </section>

    <section class="claim-confirm-card">
        <p class="claim-thanks">
            Apreciado Cliente, su reclamación ha sido registrada exitosamente. Un miembro de nuestro equipo se pondrá en contacto con usted a la brevedad posible para gestionar su caso.
        </p>
        <p>Gracias por ayudarnos a mejorar.</p>

        <div class="claim-confirm-box">
            <p><strong>Código de Confirmación:</strong> #<?php echo htmlspecialchars($reclamo["id"] ?? $codigo); ?></p>
            <p><strong>Fecha y Hora:</strong> <?php echo htmlspecialchars($reclamo["fecha"] ?? ""); ?><?php echo !empty($reclamo["hora"]) ? " - " . htmlspecialchars($reclamo["hora"]) . " hrs." : ""; ?></p>
            <p><strong>Tipo de Incidencia:</strong> <?php echo htmlspecialchars($reclamo["tipo"] ?? "Reclamo"); ?></p>
            <p><strong>Resumen:</strong> <?php echo htmlspecialchars($reclamo["descripcion"] ?? ""); ?></p>
            <p><strong>Siguientes pasos:</strong> Revise su correo electrónico para una copia detallada y para el seguimiento de su caso.</p>
        </div>

        <div class="claim-confirm-actions">
            <a href="resumen_reclamo.php?codigo=<?php echo urlencode($reclamo["id"] ?? $codigo); ?>" target="_blank">Descargar Resumen</a>
            <a href="../index.php">Volver al Inicio</a>
        </div>
    </section>
</main>

<?php include("../includes/footer.php"); ?>

</body>
</html>
