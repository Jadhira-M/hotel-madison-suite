<?php

$host = "localhost";
$usuario = "root";
$password = "";
$bd = "hotel_madison_suite";

$conn = new mysqli(
    $host,
    $usuario,
    $password,
    $bd
);

if ($conn->connect_error) {
    die("Error de conexion: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>
