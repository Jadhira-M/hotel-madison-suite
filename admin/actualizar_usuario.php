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

$id = $_POST['id'];
$nombre = trim($_POST['nombre']);
$correo = trim($_POST['correo']);
$rol = $_POST['rol'];

// Verificar si el correo ya pertenece a otro usuario
$sql = "SELECT id FROM usuarios WHERE correo = ? AND id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $correo, $id);
$stmt->execute();

$resultado = $stmt->get_result();

if($resultado->num_rows > 0){

    header("Location: editar_usuario.php?id=$id&mensaje=correo");
    exit();

}

// Actualizar usuario
$sql = "UPDATE usuarios
        SET nombre=?, correo=?, rol=?
        WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $nombre, $correo, $rol, $id);

if($stmt->execute()){

    header("Location: usuarios.php?mensaje=actualizado");
    exit();

}else{

    header("Location: usuarios.php?mensaje=error");
    exit();

}
?>