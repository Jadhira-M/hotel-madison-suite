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
include("layout/header.php");
include("layout/sidebar.php");

$id = $_GET['id'];

$sql = "SELECT * FROM habitaciones WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$habitacion = $resultado->fetch_assoc();

if (!$habitacion) {
    die("Habitación no encontrada.");
}
?>

<div class="container-fluid">

<div class="card shadow">

<div class="card-header bg-warning">

<h3>Editar Habitación</h3>

</div>

<div class="card-body">

<form action="actualizar_habitacion.php" method="POST" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?php echo $habitacion['id']; ?>">

<div class="row">

<div class="col-md-6 mb-3">

<label>Número</label>

<input
type="text"
name="numero"
class="form-control"
value="<?php echo $habitacion['numero']; ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label>Nombre</label>

<input
type="text"
name="nombre"
class="form-control"
value="<?php echo $habitacion['nombre']; ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label>Tipo</label>

<select name="tipo" class="form-select">

<?php

$tipos = ["Simple","Doble","Matrimonial","Suite","Familiar"];

foreach($tipos as $tipo){

$selected = ($habitacion['tipo']==$tipo) ? "selected" : "";

echo "<option value='$tipo' $selected>$tipo</option>";

}

?>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Precio</label>

<input
type="number"
step="0.01"
name="precio"
class="form-control"
value="<?php echo $habitacion['precio']; ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label>Capacidad</label>

<input
type="number"
name="capacidad"
class="form-control"
value="<?php echo $habitacion['capacidad']; ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label>Camas</label>

<input
type="number"
name="camas"
class="form-control"
value="<?php echo $habitacion['camas']; ?>"
required>

</div>

<div class="col-12 mb-3">

<label>Descripción</label>

<textarea
name="descripcion"
class="form-control"
rows="4"><?php echo $habitacion['descripcion']; ?></textarea>

</div>

<div class="col-md-6 mb-3">

<label>Estado</label>

<select name="estado" class="form-select">

<option value="disponible" <?php if($habitacion['estado']=="disponible") echo "selected"; ?>>

Disponible

</option>

<option value="ocupada" <?php if($habitacion['estado']=="ocupada") echo "selected"; ?>>

Ocupada

</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Nueva Imagen (opcional)</label>

<input
type="file"
name="imagen"
class="form-control">

</div>

<div class="col-12 mb-3">

<hr>

<h5>Servicios incluidos</h5>

<div class="form-check">

<input
class="form-check-input"
type="checkbox"
name="wifi"
value="1"
<?php echo !empty($habitacion["wifi"]) ? "checked" : ""; ?>>

<label class="form-check-label">
WiFi
</label>

</div>

<div class="form-check">

<input
class="form-check-input"
type="checkbox"
name="desayuno"
value="1"
<?php echo !empty($habitacion["desayuno"]) ? "checked" : ""; ?>>

<label class="form-check-label">
Desayuno
</label>

</div>

</div>

</div>

<button class="btn btn-success">

Actualizar Habitación

</button>

<a href="habitaciones.php" class="btn btn-secondary">

Cancelar

</a>

</form>

</div>

</div>

</div>

<?php include("layout/footer.php"); ?>

