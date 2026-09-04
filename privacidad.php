<?php
session_start();
include("includes/header.php");
?>

<body>

<?php include("includes/navbar.php"); ?>

<main class="policy-page">
    <section class="container policy-shell">
        <h1>Política de privacidad</h1>
        <p class="policy-intro">Madison Suite protege la información personal registrada durante reservas, consultas y reclamaciones.</p>

        <div class="policy-list">
            <article class="policy-row">
                <i class="bi bi-person-lock"></i>
                <h2>Datos recopilados</h2>
                <div>
                    <p>Podemos solicitar nombre, correo, teléfono, documento, fechas de estadía y datos necesarios para gestionar tu reserva o reclamo.</p>
                </div>
            </article>

            <article class="policy-row">
                <i class="bi bi-shield-check"></i>
                <h2>Uso de la información</h2>
                <div>
                    <p>La información se usa para confirmar reservas, atender solicitudes, emitir comprobantes y mejorar la experiencia del huésped.</p>
                </div>
            </article>

            <article class="policy-row">
                <i class="bi bi-envelope-check"></i>
                <h2>Comunicaciones</h2>
                <div>
                    <p>Podemos contactarte por correo, teléfono o WhatsApp para coordinar pagos, confirmaciones o seguimiento de atención.</p>
                </div>
            </article>

            <article class="policy-row">
                <i class="bi bi-trash3"></i>
                <h2>Derechos del usuario</h2>
                <div>
                    <p>Puedes solicitar actualización, corrección o eliminación de tus datos comunicándote con el hotel.</p>
                </div>
            </article>
        </div>
    </section>
</main>

<?php include("includes/footer.php"); ?>

</body>
</html>
