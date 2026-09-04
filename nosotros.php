<?php
session_start();

include("includes/header.php");
?>

<body>

<?php include("includes/navbar.php"); ?>

<section class="bg-dark text-white py-5">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Sobre Nosotros</h1>
        <p class="lead">Conoce la historia de Madison Suite</p>
    </div>
</section>

<section class="py-5 about-story">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <img src="assets/img/hotel.jpg" class="img-fluid rounded shadow" alt="Hotel Madison Suite">
            </div>
            <div class="col-lg-6">
                <h2 class="mb-4">Nuestra Historia</h2>
                <p>
                    Madison Suite nació con el objetivo de brindar un lugar seguro, cómodo y moderno para todos nuestros huéspedes.
                    Durante estos años hemos recibido visitantes nacionales e internacionales ofreciendo siempre una atención
                    personalizada y habitaciones de excelente calidad.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="bg-light py-5 about-purpose">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow h-100 about-purpose-card">
                    <div class="card-body">
                        <h3><i class="bi bi-bullseye"></i> Misión</h3>
                        <p>
                            Brindar un servicio de hospedaje de alta calidad, garantizando comodidad, seguridad
                            y una excelente atención para nuestros clientes.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow h-100 about-purpose-card">
                    <div class="card-body">
                        <h3><i class="bi bi-globe-americas"></i> Visión</h3>
                        <p>
                            Ser uno de los hoteles más reconocidos de Tacna, destacando por la calidad del servicio
                            y la satisfacción de nuestros huéspedes.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 about-values">
    <div class="container">
        <h2 class="text-center mb-5">Nuestros Valores</h2>

        <div class="row text-center g-4">
            <div class="col-md-3 col-sm-6">
                <div class="feature-card">
                    <h4><i class="bi bi-hand-thumbs-up-fill"></i></h4>
                    <h5>Respeto</h5>
                    <p>Tratamos a cada huésped con cordialidad.</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="feature-card">
                    <h4><i class="bi bi-star-fill"></i></h4>
                    <h5>Calidad</h5>
                    <p>Buscamos la excelencia en cada servicio.</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="feature-card">
                    <h4><i class="bi bi-shield-lock-fill"></i></h4>
                    <h5>Seguridad</h5>
                    <p>Protegemos a nuestros huéspedes las 24 horas.</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="feature-card">
                    <h4><i class="bi bi-heart-fill"></i></h4>
                    <h5>Compromiso</h5>
                    <p>Trabajamos para superar las expectativas.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include("includes/footer.php"); ?>

</body>

</html>
