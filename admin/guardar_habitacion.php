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

// Obtener datos
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

// Procesar imagen
$imagen = "";

if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0){

    $nombreImagen = time() . "_" . basename($_FILES["imagen"]["name"]);

    $rutaDestino = "../assets/img/habitaciones/" . $nombreImagen;

    move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino);

    $imagen = $nombreImagen;
}

// Insertar habitación
$sql = "INSERT INTO habitaciones
(numero,nombre,tipo,descripcion,precio,capacidad,camas,imagen,estado,wifi,desayuno,aire)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssssdisssiii",
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
    $aire
);

if($stmt->execute()){

    header("Location: habitaciones.php?mensaje=agregado");
    exit();

}else{

    echo "Error al guardar la habitación.";

}
?>
