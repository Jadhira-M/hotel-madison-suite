<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");
include("layout/header.php");
include("layout/sidebar.php");

function galeriaTableExists($conn)
{
    $database = $conn->query("SELECT DATABASE() AS db")->fetch_assoc()["db"] ?? "";
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = ?
          AND TABLE_NAME = 'galeria'
    ");
    $stmt->bind_param("s", $database);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return (int) ($row["total"] ?? 0) > 0;
}

function uploadGalleryImage($file)
{
    if (empty($file["tmp_name"]) || !is_uploaded_file($file["tmp_name"])) {
        return "";
    }

    $allowed = ["jpg", "jpeg", "png", "webp"];
    $extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowed, true)) {
        return "";
    }

    $targetDir = __DIR__ . "/../assets/img/galeria";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "_", basename($file["name"]));
    $target = $targetDir . "/" . $filename;

    if (!move_uploaded_file($file["tmp_name"], $target)) {
        return "";
    }

    return "assets/img/galeria/" . $filename;
}

$mensaje = "";
$error = "";
$tablaLista = galeriaTableExists($conn);

if ($tablaLista && $_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "";

    if ($accion === "crear") {
        $titulo = trim($_POST["titulo"] ?? "");
        $orden = (int) ($_POST["orden"] ?? 0);
        $imagen = uploadGalleryImage($_FILES["imagen"] ?? []);

        if ($titulo === "" || $imagen === "") {
            $error = "Completa el título y selecciona una imagen válida.";
        } else {
            $stmt = $conn->prepare("INSERT INTO galeria (titulo, imagen, orden, estado) VALUES (?, ?, ?, 'activo')");
            $stmt->bind_param("ssi", $titulo, $imagen, $orden);
            $mensaje = $stmt->execute() ? "Imagen agregada a la galería." : "";
            $error = $mensaje ? "" : "No se pudo agregar la imagen.";
        }
    }

    if ($accion === "actualizar") {
        $id = (int) ($_POST["id"] ?? 0);
        $titulo = trim($_POST["titulo"] ?? "");
        $orden = (int) ($_POST["orden"] ?? 0);
        $estado = ($_POST["estado"] ?? "") === "inactivo" ? "inactivo" : "activo";
        $nuevaImagen = uploadGalleryImage($_FILES["imagen"] ?? []);

        if ($id <= 0 || $titulo === "") {
            $error = "No se pudo actualizar la imagen.";
        } elseif ($nuevaImagen !== "") {
            $stmt = $conn->prepare("UPDATE galeria SET titulo = ?, imagen = ?, orden = ?, estado = ? WHERE id = ?");
            $stmt->bind_param("ssisi", $titulo, $nuevaImagen, $orden, $estado, $id);
            $mensaje = $stmt->execute() ? "Imagen actualizada." : "";
            $error = $mensaje ? "" : "No se pudo actualizar la imagen.";
        } else {
            $stmt = $conn->prepare("UPDATE galeria SET titulo = ?, orden = ?, estado = ? WHERE id = ?");
            $stmt->bind_param("sisi", $titulo, $orden, $estado, $id);
            $mensaje = $stmt->execute() ? "Imagen actualizada." : "";
            $error = $mensaje ? "" : "No se pudo actualizar la imagen.";
        }
    }
}

$imagenes = [];
if ($tablaLista) {
    $result = $conn->query("SELECT * FROM galeria ORDER BY orden ASC, id ASC");
    $imagenes = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2>Administración de Galería</h2>
            <p class="text-muted mb-0">Sube imágenes y cambia los títulos que se ven en la página pública.</p>
        </div>
        <a href="../galeria.php" class="btn btn-warning" target="_blank">
            <i class="bi bi-box-arrow-up-right"></i> Ver galería
        </a>
    </div>

    <?php if ($mensaje): ?><div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-warning"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <?php if (!$tablaLista): ?>
        <div class="alert alert-warning">
            La tabla de galería aún no existe. Ejecuta <strong>database/galeria_dinamica.sql</strong> en phpMyAdmin.
        </div>
    <?php else: ?>
        <div class="card shadow mb-4">
            <div class="card-body">
                <h4 class="mb-3">Nueva imagen</h4>
                <form method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
                    <input type="hidden" name="accion" value="crear">
                    <div class="col-md-4">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Orden</label>
                        <input type="number" name="orden" class="form-control" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Imagen</label>
                        <input type="file" name="imagen" class="form-control" accept=".jpg,.jpeg,.png,.webp" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-warning w-100">Agregar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Imagen</th>
                            <th>Título</th>
                            <th>Orden</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($imagenes as $imagen): ?>
                            <tr>
                                <td>
                                    <img src="../<?php echo htmlspecialchars($imagen["imagen"]); ?>" alt="<?php echo htmlspecialchars($imagen["titulo"]); ?>" style="width:120px;height:70px;object-fit:cover;border-radius:6px">
                                </td>
                                <td colspan="4">
                                    <form method="POST" enctype="multipart/form-data" class="row g-2 align-items-center">
                                        <input type="hidden" name="accion" value="actualizar">
                                        <input type="hidden" name="id" value="<?php echo (int) $imagen["id"]; ?>">
                                        <div class="col-md-3">
                                            <input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($imagen["titulo"]); ?>" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="orden" class="form-control" value="<?php echo (int) $imagen["orden"]; ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <select name="estado" class="form-select">
                                                <option value="activo" <?php echo $imagen["estado"] === "activo" ? "selected" : ""; ?>>Activo</option>
                                                <option value="inactivo" <?php echo $imagen["estado"] === "inactivo" ? "selected" : ""; ?>>Oculto</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="file" name="imagen" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                        </div>
                                        <div class="col-md-2 d-flex gap-2">
                                            <button type="submit" class="btn btn-warning btn-sm">Guardar</button>
                                            <a href="eliminar_galeria.php?id=<?php echo (int) $imagen["id"]; ?>" class="btn btn-danger btn-sm btn-eliminar" title="Eliminar">
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include("layout/footer.php"); ?>
