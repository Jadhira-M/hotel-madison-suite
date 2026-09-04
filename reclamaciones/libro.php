<?php
session_start();
include("../includes/header.php");
?>

<body>

<?php include("../includes/navbar.php"); ?>

<main class="claim-page">
    <section class="claim-hero">
        <h1>Libro de Reclamaciones Virtual</h1>
        <p>Conforme a lo establecido en el Código de Protección y Defensa del Consumidor, nuestra institución cuenta con un Libro de Reclamaciones a su disposición.</p>
    </section>

    <section class="claim-form-card">
        <form action="guardar_reclamo.php" method="POST">
            <fieldset>
                <legend><i class="bi bi-person"></i> 1. Identificación del Consumidor</legend>
                <div class="claim-grid">
                    <label>
                        Nombre completo:
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($_SESSION["usuario"] ?? ""); ?>" required>
                    </label>
                    <label>
                        Documento de identidad:
                        <input type="text" name="documento" required>
                    </label>
                    <label class="full">
                        Domicilio:
                        <input type="text" name="domicilio" required>
                    </label>
                    <label>
                        Correo electrónico:
                        <input type="email" name="correo" value="<?php echo htmlspecialchars($_SESSION["correo"] ?? ""); ?>" required>
                    </label>
                    <label>
                        Teléfono / celular:
                        <input type="tel" name="telefono" required>
                    </label>
                </div>
            </fieldset>

            <fieldset>
                <legend><i class="bi bi-bag-check"></i> 2. Identificación del Bien Contratado</legend>
                <div class="claim-grid">
                    <label>
                        Tipo de bien:
                        <select name="tipo_bien" required>
                            <option value="Producto">Producto</option>
                            <option value="Servicio" selected>Servicio</option>
                        </select>
                    </label>
                    <label>
                        Monto reclamado (S/):
                        <input type="number" name="monto" min="0" step="0.01" value="0">
                    </label>
                    <label class="full">
                        Descripción del bien o servicio:
                        <input type="text" name="bien_servicio" required>
                    </label>
                </div>
            </fieldset>

            <fieldset>
                <legend><i class="bi bi-file-earmark-text"></i> 3. Detalle de la Reclamación</legend>
                <div class="claim-options">
                    <label>
                        <input type="radio" name="tipo_incidencia" value="Queja" required>
                        <span>Queja</span>
                        <small>Disconformidad no relacionada directamente al producto o servicio.</small>
                    </label>
                    <label>
                        <input type="radio" name="tipo_incidencia" value="Reclamo" required checked>
                        <span>Reclamo</span>
                        <small>Disconformidad relacionada al producto o servicio.</small>
                    </label>
                </div>

                <label>
                    Detalle del reclamo o queja:
                    <textarea name="detalle" rows="4" required></textarea>
                </label>

                <label>
                    Pedido del consumidor:
                    <textarea name="pedido" rows="3" required></textarea>
                </label>

                <label class="claim-check">
                    <input type="checkbox" name="acepta" value="1" required>
                    <span>Declaro ser el autor de este reclamo y acepto que la información será usada para gestionar mi solicitud.</span>
                </label>
            </fieldset>

            <button type="submit" class="claim-submit">Enviar Reclamación</button>
        </form>
    </section>
</main>

<?php include("../includes/footer.php"); ?>

</body>
</html>
