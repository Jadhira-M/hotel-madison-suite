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

// Confirmar la reserva
$sql = "UPDATE reservas
        SET estado='confirmada'
        WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if($stmt->execute()){

    // Obtener la habitación reservada
    $sqlHabitacion = "SELECT habitacion_id
                      FROM reservas
                      WHERE id=?";

    $stmt2 = $conn->prepare($sqlHabitacion);
    $stmt2->bind_param("i",$id);
    $stmt2->execute();

    $resultado = $stmt2->get_result();
    $reserva = $resultado->fetch_assoc();

    // Cambiar la habitación a ocupada
    $sql = "UPDATE habitaciones
            SET estado='ocupada'
            WHERE id=?";

    $stmt3 = $conn->prepare($sql);
    $stmt3->bind_param("i",$reserva['habitacion_id']);
    $stmt3->execute();

}

header("Location: reservas.php");
exit();
?>