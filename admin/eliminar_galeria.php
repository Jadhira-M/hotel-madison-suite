<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM galeria WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: galeria.php");
exit();
?>
