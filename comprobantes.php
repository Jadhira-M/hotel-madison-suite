<?php
session_start();
include("includes/header.php");
?>

<body>

<?php include("includes/navbar.php"); ?>

<main class="policy-page">
    <section class="container policy-shell">
        <h1>Comprobantes electrónicos</h1>
        <p class="policy-intro">Solicita o consulta la emisión de comprobantes correspondientes a tus reservas y pagos.</p>

        <div class="policy-list">
            <article class="policy-row">
                <i class="bi bi-receipt"></i>
                <h2>Boleta o factura</h2>
                <div>
                    <p>Indica si necesitas boleta o factura al momento de confirmar tu reserva o al comunicarte con recepción.</p>
                </div>
            </article>

            <article class="policy-row">
                <i class="bi bi-building"></i>
                <h2>Datos para factura</h2>
                <div>
                    <p>Para factura, envía RUC, razón social, dirección fiscal, correo de recepción y datos de la reserva.</p>
                </div>
            </article>

            <article class="policy-row">
                <i class="bi bi-envelope-paper"></i>
                <h2>Entrega</h2>
                <div>
                    <p>El comprobante será enviado al correo registrado una vez validado el pago y completados los datos requeridos.</p>
                </div>
            </article>

            <article class="policy-row">
                <i class="bi bi-whatsapp"></i>
                <h2>Contacto</h2>
                <div>
                    <p>Para consultas sobre comprobantes, comunícate al +51 939-188-087 o escribe a hotelmadisonsuite@gmail.com.</p>
                </div>
            </article>
        </div>
    </section>
</main>

<?php include("includes/footer.php"); ?>

</body>
</html>
