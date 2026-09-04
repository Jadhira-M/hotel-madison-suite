<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once(__DIR__ . "/lang.php");

$baseUrl = $baseUrl ?? "";
$isLogged = isset($_SESSION["id_usuario"]);
$isAdmin = isset($_SESSION["rol"]) && $_SESSION["rol"] === "admin";
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold brand-with-logo" href="<?php echo $baseUrl; ?>index.php">
            <img src="<?php echo $baseUrl; ?>assets/img/emblema-short.png" alt="Madison Suite">
            <span>Madison Suite</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto main-nav">
                <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>index.php"><?php echo t("Inicio"); ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>nosotros.php"><?php echo t("Nosotros"); ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>habitaciones.php"><?php echo t("Habitaciones"); ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>servicios.php"><?php echo t("Servicios"); ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>normas.php"><?php echo t("Normas"); ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>galeria.php"><?php echo t("Galer&iacute;a"); ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>resenas.php"><?php echo t("Rese&ntilde;as"); ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>contacto.php"><?php echo t("Contacto"); ?></a></li>

                <?php if ($isLogged) { ?>
                    <li class="nav-item dropdown account-dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="accountMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo t("Mi Cuenta"); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="accountMenu">
                            <?php if (!$isAdmin) { ?>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>cliente/dashboard.php"><?php echo t("Panel de cuenta"); ?></a></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>cliente/perfil.php"><?php echo t("Mi perfil"); ?></a></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>cliente/historial.php"><?php echo t("Historial"); ?></a></li>
                                <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>cliente/cambiar_password.php"><?php echo t("Cambiar contrase&ntilde;a"); ?></a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php } ?>
                            <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>reservas/mis_reservas.php"><?php echo t("Mis Reservas"); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>reclamaciones/mis_reclamos.php"><?php echo t("Mis Reclamos"); ?></a></li>
                        </ul>
                    </li>

                    <?php if ($isAdmin) { ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>admin/dashboard.php">Admin</a></li>
                    <?php } ?>
                <?php } ?>
            </ul>

            <div class="nav-tools d-flex align-items-center gap-2 ms-lg-3 mt-3 mt-lg-0">
                <div class="nav-search-wrap">
                    <button class="nav-search-toggle" type="button" aria-label="Abrir buscador" aria-expanded="false">
                        <i class="bi bi-search"></i>
                    </button>
                    <form class="nav-search-panel" action="<?php echo $baseUrl; ?>buscar.php" method="GET" role="search">
                        <label for="siteSearchInput">Buscar en Madison Suite</label>
                        <div>
                            <input id="siteSearchInput" type="search" name="q" placeholder="Habitaciones, wifi, reclamos..." aria-label="Buscar" value="<?php echo htmlspecialchars($_GET["q"] ?? ""); ?>">
                            <button type="submit">
                                <i class="bi bi-search"></i>
                                Buscar
                            </button>
                        </div>
                    </form>
                </div>

                <div class="language-switch" aria-label="Selector de idioma">
                    <a class="<?php echo $currentLang === "es" ? "active" : ""; ?>" href="<?php echo languageUrl("es"); ?>">ES</a>
                    <span>|</span>
                    <a class="<?php echo $currentLang === "en" ? "active" : ""; ?>" href="<?php echo languageUrl("en"); ?>">EN</a>
                    <span>|</span>
                    <a class="<?php echo $currentLang === "pt" ? "active" : ""; ?>" href="<?php echo languageUrl("pt"); ?>">PT</a>
                    <span>|</span>
                    <a class="<?php echo $currentLang === "it" ? "active" : ""; ?>" href="<?php echo languageUrl("it"); ?>">IT</a>
                </div>

                <?php if ($isLogged) { ?>
                    <span class="navbar-user"><?php echo t("Hola"); ?>, <strong><?php echo htmlspecialchars($_SESSION["usuario"]); ?></strong></span>
                    <a href="<?php echo $baseUrl; ?>auth/logout.php" class="btn btn-outline-warning btn-sm"><?php echo t("Salir"); ?></a>
                <?php } else { ?>
                    <a href="<?php echo $baseUrl; ?>auth/login.php" class="btn btn-outline-warning btn-sm"><?php echo t("Iniciar sesi&oacute;n"); ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchWrap = document.querySelector(".nav-search-wrap");
    if (!searchWrap) {
        return;
    }

    const toggle = searchWrap.querySelector(".nav-search-toggle");
    const input = searchWrap.querySelector("input[type='search']");

    toggle.addEventListener("click", function () {
        const isOpen = searchWrap.classList.toggle("is-open");
        toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
        if (isOpen && input) {
            setTimeout(function () {
                input.focus();
            }, 80);
        }
    });

    document.addEventListener("click", function (event) {
        if (!searchWrap.contains(event.target)) {
            searchWrap.classList.remove("is-open");
            toggle.setAttribute("aria-expanded", "false");
        }
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            searchWrap.classList.remove("is-open");
            toggle.setAttribute("aria-expanded", "false");
        }
    });
});
</script>
