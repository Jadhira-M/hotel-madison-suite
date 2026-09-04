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

// Cancelar reserva
$sql = "UPDATE reservas
        SET estado='cancelada'
        WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);

$stmt->execute();

header("Location: reservas.php");
exit();
?>