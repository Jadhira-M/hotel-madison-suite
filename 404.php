<?php
http_response_code(404);
include("includes/header.php");
?>

<body>

<?php include("includes/navbar.php"); ?>

<style>
.error-page{
    align-items:center;
    background:#f7f5ef;
    display:flex;
    justify-content:center;
    min-height:calc(100vh - 90px);
    padding:46px 16px;
}

.error-card{
    background:
        radial-gradient(circle at 16% 18%, rgba(213,163,25,.08), transparent 26%),
        radial-gradient(circle at 82% 80%, rgba(184,122,32,.08), transparent 30%),
        linear-gradient(135deg, #fff 0%, #f8f8f5 48%, #fff 100%);
    border:4px solid #d5a319;
    box-shadow:0 14px 38px rgba(0,0,0,.14);
    max-width:760px;
    padding:30px 46px 36px;
    position:relative;
    text-align:center;
    width:100%;
}

.error-card:before,
.error-card:after{
    background:#f7f5ef;
    color:#d5a319;
    content:"✦";
    font-size:38px;
    left:50%;
    line-height:1;
    padding:0 18px;
    position:absolute;
    transform:translateX(-50%);
}

.error-card:before{top:-24px}
.error-card:after{bottom:-25px}

.error-kicker,
.error-help-title{
    color:#aa7710;
    font-family:Georgia, serif;
    font-size:24px;
    font-weight:900;
    letter-spacing:.02em;
    margin:0 0 8px;
    text-transform:uppercase;
}

.error-code{
    color:#b98b2c;
    font-family:Georgia, serif;
    font-size:54px;
    font-weight:900;
    margin:0 0 4px;
    text-transform:uppercase;
}

.error-card h2{
    color:#111;
    font-family:Georgia, serif;
    font-size:26px;
    margin:0 0 4px;
}

.error-card p{
    color:#222;
    line-height:1.35;
    margin:0 auto 16px;
    max-width:520px;
}

.error-logo{
    border-radius:50%;
    box-shadow:0 0 34px rgba(213,163,25,.24);
    display:block;
    height:128px;
    margin:16px auto 8px;
    object-fit:cover;
    width:128px;
}

.error-brand{
    color:#111;
    font-family:Georgia, serif;
    font-size:24px;
    margin:0 0 2px;
    text-transform:uppercase;
}

.error-subbrand{
    color:#111;
    font-size:15px;
    font-weight:800;
    margin:0 0 18px;
    text-transform:uppercase;
}

.error-main-action,
.error-small-action{
    background:linear-gradient(90deg, #b87a20, #f1d26a, #b87a20);
    border:0;
    border-radius:10px;
    box-shadow:0 7px 16px rgba(0,0,0,.18);
    color:#111;
    display:inline-flex;
    font-weight:800;
    justify-content:center;
    min-width:240px;
    padding:12px 22px;
    text-decoration:none;
}

.error-divider{
    align-items:center;
    color:#d5a319;
    display:flex;
    font-size:28px;
    gap:16px;
    justify-content:center;
    margin:26px auto 22px;
    max-width:260px;
}

.error-divider:before,
.error-divider:after{
    background:#d5a319;
    content:"";
    height:2px;
    width:100%;
}

.error-help-title{
    font-size:18px;
    margin-bottom:18px;
}

.error-help-actions{
    display:flex;
    gap:28px;
    justify-content:center;
}

.error-small-action{
    min-width:210px;
    padding:11px 18px;
}

@media(max-width:680px){
    .error-card{padding:32px 22px 36px}
    .error-code{font-size:42px}
    .error-help-actions{flex-direction:column;gap:14px}
    .error-main-action,.error-small-action{width:100%}
}
</style>

<main class="error-page">
    <section class="error-card" aria-labelledby="error-title">
        <p class="error-kicker">Página no encontrada</p>
        <h1 class="error-code" id="error-title">Error 404</h1>
        <h2>Oh no, algo ha salido mal.</h2>
        <p>Parece que la página que estás buscando no existe o ha sido movida.</p>

        <img class="error-logo" src="assets/img/emblema.png" alt="Emblema Madison Suite">
        <p class="error-brand">Madison Suite</p>
        <p class="error-subbrand">Tu hogar en Tacna</p>

        <a class="error-main-action" href="index.php">Volver a la Página de Inicio</a>

        <div class="error-divider" aria-hidden="true">✦</div>

        <p class="error-help-title">¿Puedo ayudarte a encontrar algo más?</p>
        <div class="error-help-actions">
            <a class="error-small-action" href="habitaciones.php">Ver Nuestras Habitaciones</a>
            <a class="error-small-action" href="contacto.php">Contactar al Hotel</a>
        </div>
    </section>
</main>

<?php include("includes/footer.php"); ?>

</body>
</html>
