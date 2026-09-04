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
?>

<div class="container-fluid">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            <h3>Agregar Habitación</h3>

        </div>

        <div class="card-body">

            <form action="guardar_habitacion.php" method="POST" enctype="multipart/form-data">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label>Número</label>

                        <input
                        type="text"
                        name="numero"
                        class="form-control"
                        required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Nombre</label>

                        <input
                        type="text"
                        name="nombre"
                        class="form-control"
                        required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Tipo</label>

                        <select
                        name="tipo"
                        class="form-select"
                        required>

                            <option value="">Seleccione...</option>

                            <option>Simple</option>

                            <option>Doble</option>

                            <option>Matrimonial</option>

                            <option>Suite</option>

                            <option>Familiar</option>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Precio (S/.)</label>

                        <input
                        type="number"
                        step="0.01"
                        name="precio"
                        class="form-control"
                        required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Capacidad</label>

                        <input
                        type="number"
                        name="capacidad"
                        class="form-control"
                        required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Camas</label>

                        <input
                        type="number"
                        name="camas"
                        class="form-control"
                        required>

                    </div>

                    <div class="col-12 mb-3">

                        <label>Descripción</label>

                        <textarea
                        name="descripcion"
                        rows="4"
                        class="form-control"
                        required></textarea>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Estado</label>

                        <select
                        name="estado"
                        class="form-select">

                            <option value="disponible">
                                Disponible
                            </option>

                            <option value="ocupada">
                                Ocupada
                            </option>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Imagen</label>

                        <input
                        type="file"
                        name="imagen"
                        class="form-control"
                        accept="image/*"
                        required>

                    </div>

                </div>

                <hr>

                <h5>Servicios incluidos</h5>

                <div class="form-check">

                    <input
                    class="form-check-input"
                    type="checkbox"
                    name="wifi"
                    value="1"
                    checked>

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
                    checked>

                    <label class="form-check-label">
                        Desayuno
                    </label>

                </div>

                <button
                type="submit"
                class="btn btn-success">

                    Guardar Habitación

                </button>

                <a
                href="habitaciones.php"
                class="btn btn-secondary">

                    Cancelar

                </a>

            </form>

        </div>

    </div>

</div>

<?php include("layout/footer.php"); ?>

