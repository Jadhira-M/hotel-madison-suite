<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");

$monthNames = [
    1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril",
    5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto",
    9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre",
];

$month = isset($_GET["mes"]) ? intval($_GET["mes"]) : intval(date("n"));
$year = isset($_GET["anio"]) ? intval($_GET["anio"]) : intval(date("Y"));

if ($month < 1 || $month > 12) {
    $month = intval(date("n"));
}

if ($year < 2020 || $year > 2100) {
    $year = intval(date("Y"));
}

$firstDay = new DateTime(sprintf("%04d-%02d-01", $year, $month));
$daysInMonth = intval($firstDay->format("t"));
$firstWeekday = intval($firstDay->format("w"));
$monthStart = $firstDay->format("Y-m-01");
$monthEnd = $firstDay->format("Y-m-t");

$previous = clone $firstDay;
$previous->modify("-1 month");

$next = clone $firstDay;
$next->modify("+1 month");

$totalRoomsResult = $conn->query("SELECT COUNT(*) AS total FROM habitaciones");
$totalRooms = $totalRoomsResult ? intval($totalRoomsResult->fetch_assoc()["total"]) : 0;

if ($totalRooms <= 0) {
    $totalRooms = 20;
}

$occupancyByDay = array_fill(1, $daysInMonth, 0);

$sql = "SELECT fecha_ingreso, fecha_salida, estado
        FROM reservas
        WHERE estado <> 'cancelada'
        AND fecha_ingreso <= ?
        AND fecha_salida >= ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $monthEnd, $monthStart);
$stmt->execute();
$reservas = $stmt->get_result();

while ($reserva = $reservas->fetch_assoc()) {
    $start = new DateTime(max($reserva["fecha_ingreso"], $monthStart));
    $end = new DateTime(min($reserva["fecha_salida"], $monthEnd));

    while ($start <= $end) {
        if (intval($start->format("n")) === $month) {
            $day = intval($start->format("j"));
            $occupancyByDay[$day]++;
        }

        $start->modify("+1 day");
    }
}

function calendarStatus($occupied, $totalRooms) {
    if ($occupied <= 0) {
        return ["label" => "Libre", "class" => "free", "value" => "Libre"];
    }

    $ratio = $occupied / max($totalRooms, 1);

    if ($ratio >= 1) {
        return ["label" => "Completo", "class" => "full", "value" => "$occupied/$totalRooms"];
    }

    if ($ratio >= .75) {
        return ["label" => "Casi lleno", "class" => "almost", "value" => "$occupied/$totalRooms"];
    }

    if ($ratio >= .4) {
        return ["label" => "Medio", "class" => "mid", "value" => "$occupied/$totalRooms"];
    }

    return ["label" => "Disponible", "class" => "low", "value" => "$occupied/$totalRooms"];
}

include("layout/header.php");
include("layout/sidebar.php");
?>

<section class="admin-page-heading">
<h1>Calendario de Ocupación</h1>
<p>Vista mensual de disponibilidad de habitaciones</p>
</section>

<div class="admin-calendar-month">
<a href="calendario.php?mes=<?php echo $previous->format("n"); ?>&anio=<?php echo $previous->format("Y"); ?>">
<i class="bi bi-chevron-left"></i>
</a>
<strong><?php echo $monthNames[$month] . " de " . $year; ?></strong>
<a href="calendario.php?mes=<?php echo $next->format("n"); ?>&anio=<?php echo $next->format("Y"); ?>">
<i class="bi bi-chevron-right"></i>
</a>
</div>

<section class="admin-calendar-card">

<div class="admin-calendar-title">
<a href="calendario.php?mes=<?php echo $previous->format("n"); ?>&anio=<?php echo $previous->format("Y"); ?>">
<i class="bi bi-chevron-left"></i>
</a>
<span><?php echo $monthNames[$month] . " de " . $year; ?></span>
<a href="calendario.php?mes=<?php echo $next->format("n"); ?>&anio=<?php echo $next->format("Y"); ?>">
<i class="bi bi-chevron-right"></i>
</a>
</div>

<div class="admin-weekdays">
<span>Dom</span><span>Lun</span><span>Mar</span><span>Mié</span><span>Jue</span><span>Vie</span><span>Sáb</span>
</div>

<div class="admin-calendar-grid">

<?php for ($i = 0; $i < $firstWeekday; $i++) { ?>
<div class="calendar-empty"></div>
<?php } ?>

<?php for ($day = 1; $day <= $daysInMonth; $day++) {
    $status = calendarStatus($occupancyByDay[$day], $totalRooms);
?>

<article class="calendar-day <?php echo $status["class"]; ?>">
<span><?php echo $day; ?></span>
<strong><?php echo $status["value"]; ?></strong>
<small><?php echo $status["label"]; ?></small>
</article>

<?php } ?>

</div>

</section>

<?php include("layout/footer.php"); ?>
