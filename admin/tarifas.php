<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");
include("../config/db_utils.php");

function numeroPost($key, $default = 0)
{
    return isset($_POST[$key]) ? max(0, (float) $_POST[$key]) : $default;
}

$mensaje = "";
$error = "";
$usaTablas = db_table_exists($conn, "tarifas_habitacion") && db_table_exists($conn, "temporadas_especiales");

if (!$usaTablas) {
    $error = "Primero ejecuta el SQL de mejoras dinámicas para activar tarifas y temporadas en MySQL.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $usaTablas) {
    $accion = $_POST["accion"] ?? "";

    if ($accion === "actualizar_tarifa") {
        $habitacionId = (int) ($_POST["tarifa_id"] ?? 0);
        $base = numeroPost("base");
        $finSemana = numeroPost("fin_semana");
        $feriados = numeroPost("feriados");

        $stmt = $conn->prepare(
            "INSERT INTO tarifas_habitacion (habitacion_id, precio_base, precio_fin_semana, precio_feriado)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                precio_base = VALUES(precio_base),
                precio_fin_semana = VALUES(precio_fin_semana),
                precio_feriado = VALUES(precio_feriado)"
        );
        $stmt->bind_param("iddd", $habitacionId, $base, $finSemana, $feriados);

        if ($stmt->execute()) {
            $stmtHabitacion = $conn->prepare("UPDATE habitaciones SET precio = ? WHERE id = ?");
            $stmtHabitacion->bind_param("di", $base, $habitacionId);
            $stmtHabitacion->execute();
            $mensaje = "Tarifa actualizada correctamente.";
        } else {
            $error = "No se pudo actualizar la tarifa.";
        }
    }

    if ($accion === "guardar_temporada") {
        $temporadaId = (int) ($_POST["temporada_id"] ?? 0);
        $nombre = trim($_POST["nombre"] ?? "");
        $fechaInicio = $_POST["fecha_inicio"] ?? "";
        $fechaFin = $_POST["fecha_fin"] ?? "";
        $multiplicador = max(1, (float) ($_POST["multiplicador"] ?? 1));

        if ($nombre === "" || $fechaInicio === "" || $fechaFin === "") {
            $error = "Completa el nombre y las fechas de la temporada.";
        } elseif ($temporadaId > 0) {
            $stmt = $conn->prepare("UPDATE temporadas_especiales SET nombre = ?, fecha_inicio = ?, fecha_fin = ?, multiplicador = ? WHERE id = ?");
            $stmt->bind_param("sssdi", $nombre, $fechaInicio, $fechaFin, $multiplicador, $temporadaId);
            $mensaje = $stmt->execute() ? "Temporada actualizada correctamente." : "";
            $error = $mensaje ? "" : "No se pudo actualizar la temporada.";
        } else {
            $stmt = $conn->prepare("INSERT INTO temporadas_especiales (nombre, fecha_inicio, fecha_fin, multiplicador, estado) VALUES (?, ?, ?, ?, 'activa')");
            $stmt->bind_param("sssd", $nombre, $fechaInicio, $fechaFin, $multiplicador);
            $mensaje = $stmt->execute() ? "Temporada creada correctamente." : "";
            $error = $mensaje ? "" : "No se pudo crear la temporada.";
        }
    }
}

$editTarifaId = (int) ($_GET["edit_tarifa"] ?? 0);
$editTemporadaId = (int) ($_GET["edit_temporada"] ?? 0);
$crearTemporada = isset($_GET["new_temporada"]);
$tarifas = [];
$temporadas = [];
$tarifaEditar = null;
$temporadaEditar = null;

if ($usaTablas) {
    $conn->query("
        UPDATE habitaciones h
        INNER JOIN tarifas_habitacion t ON t.habitacion_id = h.id
        SET h.precio = t.precio_base
    ");

    $sqlTarifas = "SELECT
                    h.id,
                    h.nombre AS habitacion,
                    COALESCE(t.precio_base, h.precio) AS base,
                    COALESCE(t.precio_fin_semana, h.precio) AS fin_semana,
                    COALESCE(t.precio_feriado, h.precio) AS feriados
                   FROM habitaciones h
                   LEFT JOIN tarifas_habitacion t ON t.habitacion_id = h.id
                   ORDER BY h.id ASC";
    $tarifas = db_fetch_all($conn->query($sqlTarifas));

    $temporadas = db_fetch_all($conn->query("SELECT * FROM temporadas_especiales WHERE estado = 'activa' ORDER BY fecha_inicio ASC"));

    foreach ($tarifas as $tarifa) {
        if ((int) $tarifa["id"] === $editTarifaId) {
            $tarifaEditar = $tarifa;
            break;
        }
    }

    foreach ($temporadas as $temporada) {
        if ((int) $temporada["id"] === $editTemporadaId) {
            $temporadaEditar = $temporada;
            break;
        }
    }
}

include("layout/header.php");
include("layout/sidebar.php");
?>

<section class="admin-page-heading">
    <h1>Gestión de Tarifas</h1>
    <p>Administra precios según temporada y tipo de día</p>
</section>

<?php if ($mensaje): ?>
    <div class="admin-alert admin-alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="admin-alert admin-alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<h2 class="admin-section-title">Tarifas Base por Habitación</h2>

<div class="admin-gold-table-wrap">
    <table class="admin-gold-table">
        <thead>
            <tr>
                <th>Habitación</th>
                <th>Precio Base (L-J)</th>
                <th>Fin de Semana (V-D)</th>
                <th>Feriados</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tarifas as $tarifa): ?>
                <tr>
                    <td><?php echo htmlspecialchars($tarifa["habitacion"]); ?></td>
                    <td>S/ <?php echo number_format((float) $tarifa["base"], 0); ?></td>
                    <td>S/ <?php echo number_format((float) $tarifa["fin_semana"], 0); ?></td>
                    <td>S/ <?php echo number_format((float) $tarifa["feriados"], 0); ?></td>
                    <td>
                        <a class="admin-action-icon" href="tarifas.php?edit_tarifa=<?php echo (int) $tarifa["id"]; ?>#editar-tarifa" title="Editar tarifa">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($tarifaEditar): ?>
    <form class="admin-edit-form" id="editar-tarifa" method="post" action="tarifas.php#editar-tarifa">
        <input type="hidden" name="accion" value="actualizar_tarifa">
        <input type="hidden" name="tarifa_id" value="<?php echo (int) $tarifaEditar["id"]; ?>">
        <h3>Editar <?php echo htmlspecialchars($tarifaEditar["habitacion"]); ?></h3>
        <div class="admin-form-grid">
            <label>
                Precio Base (L-J)
                <input type="number" min="0" step="1" name="base" value="<?php echo htmlspecialchars($tarifaEditar["base"]); ?>" required>
            </label>
            <label>
                Fin de Semana (V-D)
                <input type="number" min="0" step="1" name="fin_semana" value="<?php echo htmlspecialchars($tarifaEditar["fin_semana"]); ?>" required>
            </label>
            <label>
                Feriados
                <input type="number" min="0" step="1" name="feriados" value="<?php echo htmlspecialchars($tarifaEditar["feriados"]); ?>" required>
            </label>
        </div>
        <div class="admin-form-actions">
            <a class="admin-light-button" href="tarifas.php">Cancelar</a>
            <button class="admin-gold-button" type="submit">Guardar cambios</button>
        </div>
    </form>
<?php endif; ?>

<div class="admin-section-row">
    <h2 class="admin-section-title mb-0">Temporadas Especiales</h2>
    <a class="admin-gold-button" href="tarifas.php?new_temporada=1#temporada-form"><i class="bi bi-plus"></i> Nueva Temporada</a>
</div>

<div class="admin-season-list">
    <?php foreach ($temporadas as $temporada): ?>
        <article>
            <div>
                <strong><?php echo htmlspecialchars($temporada["nombre"]); ?></strong>
                <small><i class="bi bi-calendar2"></i> <?php echo htmlspecialchars($temporada["fecha_inicio"]); ?> - <?php echo htmlspecialchars($temporada["fecha_fin"]); ?></small>
            </div>
            <span>Multiplicador <b>x<?php echo htmlspecialchars($temporada["multiplicador"]); ?></b></span>
            <a class="admin-action-icon" href="tarifas.php?edit_temporada=<?php echo (int) $temporada["id"]; ?>#temporada-form" title="Editar temporada">
                <i class="bi bi-pencil-fill"></i>
            </a>
        </article>
    <?php endforeach; ?>
</div>

<?php if ($crearTemporada || $temporadaEditar): ?>
    <?php
    $temporadaForm = $temporadaEditar ?: [
        "id" => 0,
        "nombre" => "",
        "fecha_inicio" => "",
        "fecha_fin" => "",
        "multiplicador" => 1.2,
    ];
    ?>
    <form class="admin-edit-form" id="temporada-form" method="post" action="tarifas.php#temporada-form">
        <input type="hidden" name="accion" value="guardar_temporada">
        <input type="hidden" name="temporada_id" value="<?php echo (int) $temporadaForm["id"]; ?>">
        <h3><?php echo $temporadaEditar ? "Editar Temporada" : "Nueva Temporada"; ?></h3>
        <div class="admin-form-grid admin-form-grid-4">
            <label>
                Nombre
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($temporadaForm["nombre"]); ?>" required>
            </label>
            <label>
                Fecha inicio
                <input type="date" name="fecha_inicio" value="<?php echo htmlspecialchars($temporadaForm["fecha_inicio"]); ?>" required>
            </label>
            <label>
                Fecha fin
                <input type="date" name="fecha_fin" value="<?php echo htmlspecialchars($temporadaForm["fecha_fin"]); ?>" required>
            </label>
            <label>
                Multiplicador
                <input type="number" min="1" step="0.1" name="multiplicador" value="<?php echo htmlspecialchars($temporadaForm["multiplicador"]); ?>" required>
            </label>
        </div>
        <div class="admin-form-actions">
            <a class="admin-light-button" href="tarifas.php">Cancelar</a>
            <button class="admin-gold-button" type="submit"><?php echo $temporadaEditar ? "Guardar cambios" : "Crear temporada"; ?></button>
        </div>
    </form>
<?php endif; ?>

<?php include("layout/footer.php"); ?>
