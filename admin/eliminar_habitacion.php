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

$id = $_GET['id'];

// Buscar la imagen
$sql = "SELECT imagen FROM habitaciones WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$habitacion = $resultado->fetch_assoc();

// Eliminar imagen
if ($habitacion && !empty($habitacion['imagen'])) {

    $ruta = "../assets/img/habitaciones/" . $habitacion['imagen'];

    if (file_exists($ruta)) {
        unlink($ruta);
    }

}

// Eliminar registro
$sql = "DELETE FROM habitaciones WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

if($stmt->execute()){

    header("Location: habitaciones.php?mensaje=eliminado");
    exit();

}else{

    echo "Error al eliminar.";

}
?>