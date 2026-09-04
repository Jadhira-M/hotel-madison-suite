<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: ../auth/login.php");
    exit();
}

if($_SESSION['rol'] != 'admin'){
    die("Acceso denegado");
}
?>