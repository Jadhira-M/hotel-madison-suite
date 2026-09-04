<?php
session_start();

if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");

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

function imagePath($imagen)
{
    return "../assets/img/habitaciones/" . basename($imagen);
}

$habitacionId = (int) ($_GET["id"] ?? $_POST["habitacion_id"] ?? 0);

$stmt = $conn->prepare("SELECT * FROM habitaciones WHERE id = ?");
$stmt->bind_param("i", $habitacionId);
$stmt->execute();
$habitacion = $stmt->get_result()->fetch_assoc();

if (!$habitacion) {
    header("Location: habitaciones.php");
    exit();
}

$galleryReady = tableExists($conn, "habitacion_imagenes");
$mensaje = "";
$tipoMensaje = "success";

if ($_SERVER["REQUEST_METHOD"] === "POST" && $galleryReady) {
    if (isset($_POST["delete_image"])) {
        $imageId = (int) $_POST["delete_image"];

        $stmtImg = $conn->prepare("SELECT imagen FROM habitacion_imagenes WHERE id = ? AND habitacion_id = ?");
        $stmtImg->bind_param("ii", $imageId, $habitacionId);
        $stmtImg->execute();
        $image = $stmtImg->get_result()->fetch_assoc();

        if ($image) {
            $file = imagePath($image["imagen"]);
            if (is_file($file)) {
                unlink($file);
            }

            $stmtDelete = $conn->prepare("DELETE FROM habitacion_imagenes WHERE id = ? AND habitacion_id = ?");
            $stmtDelete->bind_param("ii", $imageId, $habitacionId);
            $stmtDelete->execute();
            $mensaje = "Imagen eliminada correctamente.";
        }
    }

    if (isset($_FILES["imagenes"]) && is_array($_FILES["imagenes"]["name"])) {
        $uploaded = 0;
        $allowed = ["jpg", "jpeg", "png", "webp"];

        foreach ($_FILES["imagenes"]["name"] as $index => $originalName) {
            if ($_FILES["imagenes"]["error"][$index] !== UPLOAD_ERR_OK || trim($originalName) === "") {
                continue;
            }

            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if (!in_array($extension, $allowed, true)) {
                continue;
            }

            $safeName = preg_replace("/[^a-zA-Z0-9._-]/", "_", basename($originalName));
            $newName = time() . "_" . $habitacionId . "_" . $index . "_" . $safeName;
            $destination = "../assets/img/habitaciones/" . $newName;

            if (move_uploaded_file($_FILES["imagenes"]["tmp_name"][$index], $destination)) {
                $stmtInsert = $conn->prepare("INSERT INTO habitacion_imagenes (habitacion_id, imagen, orden) VALUES (?, ?, ?)");
                $order = $index + 1;
                $stmtInsert->bind_param("isi", $habitacionId, $newName, $order);
                $stmtInsert->execute();
                $uploaded++;
            }
        }

        if ($uploaded > 0) {
            $mensaje = $uploaded . " imagen(es) agregada(s) correctamente.";
        } elseif ($mensaje === "") {
            $tipoMensaje = "warning";
            $mensaje = "No se subió ninguna imagen válida. Usa JPG, PNG o WEBP.";
        }
    }
}

$imagenes = [];
if ($galleryReady) {
    $stmtGallery = $conn->prepare("SELECT * FROM habitacion_imagenes WHERE habitacion_id = ? ORDER BY orden ASC, id ASC");
    $stmtGallery->bind_param("i", $habitacionId);
    $stmtGallery->execute();
    $imagenes = $stmtGallery->get_result()->fetch_all(MYSQLI_ASSOC);
}

include("layout/header.php");
include("layout/sidebar.php");
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Imágenes de habitación</h2>
            <p class="text-muted mb-0"><?php echo htmlspecialchars($habitacion["nombre"]); ?></p>
        </div>
        <a href="habitaciones.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <?php if (!$galleryReady): ?>
        <div class="alert alert-warning">
            Primero ejecuta el SQL <strong>database/habitacion_imagenes.sql</strong> en phpMyAdmin para activar esta galería.
        </div>
    <?php endif; ?>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo htmlspecialchars($tipoMensaje); ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header">
            <h3 class="mb-0">Subir nuevas imágenes</h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="habitacion_id" value="<?php echo (int) $habitacionId; ?>">
                <label class="form-label">Selecciona una o varias imágenes</label>
                <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple <?php echo !$galleryReady ? "disabled" : ""; ?>>
                <div class="form-text">Estas fotos aparecerán en el detalle público de la habitación.</div>
                <button class="btn btn-warning mt-3" <?php echo !$galleryReady ? "disabled" : ""; ?>>
                    <i class="bi bi-cloud-arrow-up"></i> Guardar imágenes
                </button>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header">
            <h3 class="mb-0">Galería actual</h3>
        </div>
        <div class="card-body">
            <?php if (!$imagenes): ?>
                <p class="text-muted mb-0">Todavía no hay imágenes extra. Se mostrará la imagen principal de la habitación.</p>
            <?php else: ?>
                <div class="admin-room-gallery">
                    <?php foreach ($imagenes as $imagen): ?>
                        <article>
                            <img src="<?php echo htmlspecialchars(imagePath($imagen["imagen"])); ?>" alt="Imagen de habitación">
                            <form method="POST">
                                <input type="hidden" name="habitacion_id" value="<?php echo (int) $habitacionId; ?>">
                                <button name="delete_image" value="<?php echo (int) $imagen["id"]; ?>" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php include("layout/footer.php"); ?>
