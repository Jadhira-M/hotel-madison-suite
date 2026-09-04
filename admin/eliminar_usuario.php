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

// Evitar que el admin se elimine a sí mismo
if ($id == $_SESSION['id_usuario']) {
    header("Location: usuarios.php?mensaje=no_eliminar");
    exit();
}

$sql = "DELETE FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: usuarios.php?mensaje=eliminado");
    exit();
} else {
    header("Location: usuarios.php?mensaje=error_eliminar");
    exit();
}
?>