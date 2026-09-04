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
                "fecha" => $row["fecha"] ?? "",
                "hora" => "",
                "huesped" => $row["nombre"] ?? "",
                "documento" => $row["documento"] ?? "",
                "correo" => $row["correo"] ?? "",
                "telefono" => $row["telefono"] ?? "",
                "tipo" => $row["tipo"] ?? "Reclamo",
                "tipo_bien" => $row["tipo_bien"] ?? "Servicio",
                "bien_servicio" => $row["bien_servicio"] ?? "",
                "monto" => $row["monto"] ?? "0",
                "descripcion" => $row["detalle"] ?? "",
                "pedido" => $row["pedido"] ?? "",
                "estado" => $row["estado"] ?? "pendiente",
            ];
        }
    }
}

if (!$reclamo) {
    header("Location: libro.php");
    exit();
}

$estado = strtoupper($reclamo["estado"] ?? "pendiente");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Reclamo - Madison Suite</title>
    <link rel="icon" type="image/png" href="../assets/img/emblema.png">
    <style>
        body{background:#f6f2e9;color:#111;font-family:Arial,sans-serif;margin:0;padding:28px}
        .sheet{background:white;border:4px solid #c68d1f;box-shadow:0 14px 34px rgba(0,0,0,.18);margin:auto;max-width:760px;padding:36px}
        .logo{border-radius:50%;display:block;height:112px;margin:0 auto 14px;object-fit:cover;width:112px}
        h1{color:#a36b08;font-family:Georgia,serif;font-size:30px;margin:0 0 8px;text-align:center;text-transform:uppercase}
        .code{border-bottom:1px solid #d6a843;border-top:1px solid #d6a843;color:#a36b08;font-size:20px;font-weight:800;margin:22px 0;padding:14px;text-align:center}
        .grid{display:grid;gap:10px;margin:24px 0}
        .row{border-bottom:1px solid #ead7ad;display:grid;grid-template-columns:210px 1fr;gap:18px;padding:10px 0}
        .label{font-weight:800}
        .status{background:#e3b72d;border-radius:6px;display:inline-block;font-weight:900;padding:4px 10px}
        .actions{display:flex;gap:12px;justify-content:center;margin-top:28px}
        button,a{background:linear-gradient(90deg,#bd7b20,#f4d86d);border:0;border-radius:8px;color:#111;cursor:pointer;font-weight:900;padding:12px 20px;text-decoration:none}
        @media(max-width:640px){body{padding:12px}.sheet{padding:22px}.row{grid-template-columns:1fr;gap:4px}}
        @media print{body{background:white;padding:0}.sheet{border:3px solid #c68d1f;box-shadow:none;max-width:none}.actions{display:none}@page{margin:12mm}}
    </style>
</head>
<body>
    <main class="sheet">
        <img class="logo" src="../assets/img/emblema.png" alt="Madison Suite">
        <h1>Resumen de Reclamo</h1>
        <p class="code">Código de Confirmación: #<?php echo htmlspecialchars($reclamo["id"] ?? $codigo); ?></p>

        <section class="grid">
            <div class="row"><span class="label">Fecha:</span><span><?php echo htmlspecialchars($reclamo["fecha"] ?? ""); ?><?php echo !empty($reclamo["hora"]) ? " - " . htmlspecialchars($reclamo["hora"]) . " hrs." : ""; ?></span></div>
            <div class="row"><span class="label">Cliente:</span><span><?php echo htmlspecialchars($reclamo["huesped"] ?? ""); ?></span></div>
            <div class="row"><span class="label">Documento:</span><span><?php echo htmlspecialchars($reclamo["documento"] ?? ""); ?></span></div>
            <div class="row"><span class="label">Correo:</span><span><?php echo htmlspecialchars($reclamo["correo"] ?? ""); ?></span></div>
            <div class="row"><span class="label">Teléfono:</span><span><?php echo htmlspecialchars($reclamo["telefono"] ?? ""); ?></span></div>
            <div class="row"><span class="label">Tipo de incidencia:</span><span><?php echo htmlspecialchars($reclamo["tipo"] ?? "Reclamo"); ?></span></div>
            <div class="row"><span class="label">Bien contratado:</span><span><?php echo htmlspecialchars($reclamo["tipo_bien"] ?? "Servicio"); ?> - <?php echo htmlspecialchars($reclamo["bien_servicio"] ?? ""); ?></span></div>
            <div class="row"><span class="label">Monto reclamado:</span><span>S/ <?php echo htmlspecialchars((string) ($reclamo["monto"] ?? "0")); ?></span></div>
            <div class="row"><span class="label">Detalle:</span><span><?php echo nl2br(htmlspecialchars($reclamo["descripcion"] ?? "")); ?></span></div>
            <div class="row"><span class="label">Pedido del consumidor:</span><span><?php echo nl2br(htmlspecialchars($reclamo["pedido"] ?? "")); ?></span></div>
            <div class="row"><span class="label">Estado:</span><span><span class="status"><?php echo htmlspecialchars($estado); ?></span></span></div>
        </section>

        <div class="actions">
            <button type="button" onclick="window.print()">Imprimir o guardar PDF</button>
            <a href="confirmar_reclamo.php?codigo=<?php echo urlencode($reclamo["id"] ?? $codigo); ?>">Volver</a>
        </div>
    </main>
</body>
</html>
