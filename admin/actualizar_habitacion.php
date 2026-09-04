<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION['rol'] != "admin") {
    header("Location: ../index.php");
    exit();
}

include("../config/conexion.php");
include("../config/db_utils.php");

$id = $_POST['id'];
$numero = $_POST['numero'];
$nombre = $_POST['nombre'];
$tipo = $_POST['tipo'];
$descripcion = $_POST['descripcion'];
$precio = $_POST['precio'];
$capacidad = $_POST['capacidad'];
$camas = $_POST['camas'];
$estado = $_POST['estado'];
$wifi = isset($_POST['wifi']) ? 1 : 0;
$desayuno = isset($_POST['desayuno']) ? 1 : 0;
$aire = 0;

$sqlImagen = "SELECT imagen FROM habitaciones WHERE id = ?";
$stmtImagen = $conn->prepare($sqlImagen);
$stmtImagen->bind_param("i", $id);
$stmtImagen->execute();
$resultado = $stmtImagen->get_result();
$habitacion = $resultado->fetch_assoc();

$imagen = $habitacion['imagen'];

// Si el usuario subió una nueva imagen
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {

    // Eliminar imagen anterior
    if (!empty($imagen) && file_exists("../assets/img/habitaciones/" . $imagen)) {
        unlink("../assets/img/habitaciones/" . $imagen);
    }

    // Guardar nueva imagen
    $nombreImagen = time() . "_" . basename($_FILES["imagen"]["name"]);

    move_uploaded_file(
        $_FILES["imagen"]["tmp_name"],
        "../assets/img/habitaciones/" . $nombreImagen
    );

    $imagen = $nombreImagen;
}

$sql = "UPDATE habitaciones SET
numero=?,
nombre=?,
tipo=?,
descripcion=?,
precio=?,
capacidad=?,
camas=?,
imagen=?,
estado=?,
wifi=?,
desayuno=?,
aire=?
WHERE id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssssdiissiiii",
    $numero,
    $nombre,
    $tipo,
    $descripcion,
    $precio,
    $capacidad,
    $camas,
    $imagen,
    $estado,
    $wifi,
    $desayuno,
    $aire,
    $id
);

if ($stmt->execute()) {
    if (db_table_exists($conn, "tarifas_habitacion")) {
        $stmtTarifa = $conn->prepare(
            "INSERT INTO tarifas_habitacion (habitacion_id, precio_base, precio_fin_semana, precio_feriado)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                precio_base = VALUES(precio_base)"
        );
        $stmtTarifa->bind_param("iddd", $id, $precio, $precio, $precio);
        $stmtTarifa->execute();
    }

    header("Location: habitaciones.php?mensaje=actualizado");
    exit();

} else {

    echo "Error al actualizar.";

}
?>
