<?php

session_start();

if(!isset($_SESSION['id_usuario'])){
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");

$id = $_GET['id'];

$sql = "UPDATE reservas
SET estado='cancelada'
WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);

$stmt->execute();

header("Location: mis_reservas.php");
exit();

?>