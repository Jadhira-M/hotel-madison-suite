<?php

session_start();

session_regenerate_id(true);

if(!isset($_SESSION['usuario'])){
    header("Location: auth/login.php");
    exit();
}
?>