<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/conexion.php");

$habitaciones = [];
$idSeleccionado = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

$sql = "SELECT * FROM habitaciones ORDER BY precio ASC";
$resultado = $conn->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $habitaciones[] = $fila;
    }
}

if (!$idSeleccionado && isset($habitaciones[0]["id"])) {
    $idSeleccionado = (int) $habitaciones[0]["id"];
}

$idsDisponibles = array_map(fn($habitacion) => (int) $habitacion["id"], $habitaciones);
if ($idSeleccionado && !in_array($idSeleccionado, $idsDisponibles, true) && isset($habitaciones[0]["id"])) {
    $idSeleccionado = (int) $habitaciones[0]["id"];
}

$ocupaciones = [];
$sqlOcupaciones = "
    SELECT habitacion_id, fecha_ingreso, fecha_salida
    FROM reservas
    WHERE estado <> 'cancelada'
      AND fecha_salida >= CURDATE()
    ORDER BY fecha_ingreso ASC
";
$resultadoOcupaciones = $conn->query($sqlOcupaciones);

if ($resultadoOcupaciones) {
    while ($fila = $resultadoOcupaciones->fetch_assoc()) {
        $habitacionId = (int) $fila["habitacion_id"];
        if (!isset($ocupaciones[$habitacionId])) {
            $ocupaciones[$habitacionId] = [];
        }
        $ocupaciones[$habitacionId][] = [
            "desde" => $fila["fecha_ingreso"],
            "hasta" => $fila["fecha_salida"],
        ];
    }
}

include("../includes/header.php");

$reservationI18n = [
    "es" => [
        "stepDates" => "Fechas",
        "stepData" => "Datos",
        "stepPayment" => "Pago",
        "roomType" => "Tipo de Habitación",
        "night" => "noche",
        "nights" => "Noches",
        "total" => "Total",
        "room" => "Habitación",
        "guests" => "Huéspedes",
        "people" => "personas",
        "bookingDetails" => "Detalles de tu Reserva",
        "personalInfo" => "Información Personal",
        "lastName" => "Apellido",
        "phone" => "Teléfono",
        "country" => "País de Procedencia",
        "finalSummary" => "Resumen Final",
        "totalPay" => "Total a Pagar",
        "confirmBooking" => "Confirmar Reserva",
        "availabilityTitle" => "Disponibilidad de esta habitación",
        "selectRoomDates" => "Selecciona una habitación y tus fechas para revisar si está disponible.",
        "selectRoom" => "Selecciona una habitación para revisar sus fechas.",
        "unavailableTitle" => "Fechas no disponibles",
        "occupiedFrom" => "Esta habitación está ocupada del",
        "to" => "al",
        "availableFrom" => "Disponible desde el",
        "chooseOtherDates" => "Elige otras fechas para continuar.",
        "upcomingOccupied" => "Fechas ocupadas próximas:",
        "occupiedItem" => "Ocupada del",
        "noUpcoming" => "No tiene reservas próximas registradas.",
        "availableTitle" => "Fechas disponibles",
        "availableRange" => "Esta habitación está disponible del",
        "dateErrorTitle" => "Fechas por corregir",
        "dateErrorText" => "La fecha de salida debe ser posterior a la fecha de entrada para calcular correctamente tu estadía.",
        "capacityTitle" => "Capacidad superada",
        "capacityText" => "La cantidad de huéspedes supera la capacidad de la habitación seleccionada. Puedes reducir huéspedes o elegir otra habitación.",
        "busyTitle" => "Fechas no disponibles",
        "busyText" => "Esta habitación ya tiene una reserva en el rango elegido. Revisa el recuadro de disponibilidad y selecciona otras fechas.",
        "modalFallback" => "Hay un detalle pendiente por revisar.",
        "understood" => "Entendido",
        "adults" => "adulto(s)",
        "children" => "niño(s)",
        "payInstructionsDefault" => "Selecciona un método de pago para ver las instrucciones.",
        "payTransferTitle" => "Instrucciones de Pago",
        "bankData" => "Datos bancarios:",
        "bank" => "Banco:",
        "account" => "Cuenta:",
        "holder" => "Titular:",
        "sendEmail" => "Envía el comprobante a hotelmadisonsuite@gmail.com",
        "makeTransfer" => "Realiza la transferencia a:",
        "sendWhatsapp" => "Envía el comprobante por WhatsApp para confirmar tu reserva.",
    ],
    "en" => [
        "stepDates" => "Dates",
        "stepData" => "Details",
        "stepPayment" => "Payment",
        "roomType" => "Room Type",
        "night" => "night",
        "nights" => "Nights",
        "total" => "Total",
        "room" => "Room",
        "guests" => "Guests",
        "people" => "people",
        "bookingDetails" => "Your Booking Details",
        "personalInfo" => "Personal Information",
        "lastName" => "Last name",
        "phone" => "Phone",
        "country" => "Country of Origin",
        "finalSummary" => "Final Summary",
        "totalPay" => "Total to Pay",
        "confirmBooking" => "Confirm Booking",
        "availabilityTitle" => "Room availability",
        "selectRoomDates" => "Select a room and your dates to check availability.",
        "selectRoom" => "Select a room to review its dates.",
        "unavailableTitle" => "Dates not available",
        "occupiedFrom" => "This room is occupied from",
        "to" => "to",
        "availableFrom" => "Available from",
        "chooseOtherDates" => "Choose other dates to continue.",
        "upcomingOccupied" => "Upcoming occupied dates:",
        "occupiedItem" => "Occupied from",
        "noUpcoming" => "No upcoming bookings registered.",
        "availableTitle" => "Dates available",
        "availableRange" => "This room is available from",
        "dateErrorTitle" => "Check your dates",
        "dateErrorText" => "The check-out date must be after the check-in date to calculate your stay correctly.",
        "capacityTitle" => "Capacity exceeded",
        "capacityText" => "The number of guests exceeds the selected room capacity. You can reduce guests or choose another room.",
        "busyTitle" => "Dates not available",
        "busyText" => "This room already has a booking in the selected range. Check the availability box and choose other dates.",
        "modalFallback" => "There is one detail pending review.",
        "understood" => "Understood",
        "adults" => "adult(s)",
        "children" => "child(ren)",
        "payInstructionsDefault" => "Select a payment method to see the instructions.",
        "payTransferTitle" => "Payment Instructions",
        "bankData" => "Bank details:",
        "bank" => "Bank:",
        "account" => "Account:",
        "holder" => "Holder:",
        "sendEmail" => "Send the receipt to hotelmadisonsuite@gmail.com",
        "makeTransfer" => "Make the transfer to:",
        "sendWhatsapp" => "Send the receipt by WhatsApp to confirm your booking.",
    ],
    "pt" => [
        "stepDates" => "Datas",
        "stepData" => "Dados",
        "stepPayment" => "Pagamento",
        "roomType" => "Tipo de Quarto",
        "night" => "noite",
        "nights" => "Noites",
        "total" => "Total",
        "room" => "Quarto",
        "guests" => "Hóspedes",
        "people" => "pessoas",
        "bookingDetails" => "Detalhes da sua Reserva",
        "personalInfo" => "Informações Pessoais",
        "lastName" => "Sobrenome",
        "phone" => "Telefone",
        "country" => "País de Origem",
        "finalSummary" => "Resumo Final",
        "totalPay" => "Total a Pagar",
        "confirmBooking" => "Confirmar Reserva",
        "availabilityTitle" => "Disponibilidade deste quarto",
        "selectRoomDates" => "Selecione um quarto e suas datas para verificar se está disponível.",
        "selectRoom" => "Selecione um quarto para revisar suas datas.",
        "unavailableTitle" => "Datas não disponíveis",
        "occupiedFrom" => "Este quarto está ocupado de",
        "to" => "a",
        "availableFrom" => "Disponível a partir de",
        "chooseOtherDates" => "Escolha outras datas para continuar.",
        "upcomingOccupied" => "Próximas datas ocupadas:",
        "occupiedItem" => "Ocupado de",
        "noUpcoming" => "Não há próximas reservas registradas.",
        "availableTitle" => "Datas disponíveis",
        "availableRange" => "Este quarto está disponível de",
        "dateErrorTitle" => "Corrija as datas",
        "dateErrorText" => "A data de saída deve ser posterior à data de entrada para calcular corretamente sua estadia.",
        "capacityTitle" => "Capacidade excedida",
        "capacityText" => "A quantidade de hóspedes supera a capacidade do quarto selecionado. Você pode reduzir hóspedes ou escolher outro quarto.",
        "busyTitle" => "Datas não disponíveis",
        "busyText" => "Este quarto já possui uma reserva no período escolhido. Revise a disponibilidade e escolha outras datas.",
        "modalFallback" => "Há um detalhe pendente para revisar.",
        "understood" => "Entendido",
        "adults" => "adulto(s)",
        "children" => "criança(s)",
        "payInstructionsDefault" => "Selecione um método de pagamento para ver as instruções.",
        "payTransferTitle" => "Instruções de Pagamento",
        "bankData" => "Dados bancários:",
        "bank" => "Banco:",
        "account" => "Conta:",
        "holder" => "Titular:",
        "sendEmail" => "Envie o comprovante para hotelmadisonsuite@gmail.com",
        "makeTransfer" => "Realize a transferência para:",
        "sendWhatsapp" => "Envie o comprovante por WhatsApp para confirmar sua reserva.",
    ],
    "it" => [
        "stepDates" => "Date",
        "stepData" => "Dati",
        "stepPayment" => "Pagamento",
        "roomType" => "Tipo di Camera",
        "night" => "notte",
        "nights" => "Notti",
        "total" => "Totale",
        "room" => "Camera",
        "guests" => "Ospiti",
        "people" => "persone",
        "bookingDetails" => "Dettagli della tua Prenotazione",
        "personalInfo" => "Informazioni Personali",
        "lastName" => "Cognome",
        "phone" => "Telefono",
        "country" => "Paese di Provenienza",
        "finalSummary" => "Riepilogo Finale",
        "totalPay" => "Totale da Pagare",
        "confirmBooking" => "Conferma Prenotazione",
        "availabilityTitle" => "Disponibilità della camera",
        "selectRoomDates" => "Seleziona una camera e le date per verificare la disponibilità.",
        "selectRoom" => "Seleziona una camera per controllare le date.",
        "unavailableTitle" => "Date non disponibili",
        "occupiedFrom" => "Questa camera è occupata dal",
        "to" => "al",
        "availableFrom" => "Disponibile dal",
        "chooseOtherDates" => "Scegli altre date per continuare.",
        "upcomingOccupied" => "Prossime date occupate:",
        "occupiedItem" => "Occupata dal",
        "noUpcoming" => "Non ci sono prenotazioni future registrate.",
        "availableTitle" => "Date disponibili",
        "availableRange" => "Questa camera è disponibile dal",
        "dateErrorTitle" => "Correggi le date",
        "dateErrorText" => "La data di uscita deve essere successiva alla data di ingresso per calcolare correttamente il soggiorno.",
        "capacityTitle" => "Capacità superata",
        "capacityText" => "Il numero di ospiti supera la capacità della camera selezionata. Puoi ridurre gli ospiti o scegliere un'altra camera.",
        "busyTitle" => "Date non disponibili",
        "busyText" => "Questa camera ha già una prenotazione nel periodo scelto. Controlla la disponibilità e scegli altre date.",
        "modalFallback" => "C'è un dettaglio da rivedere.",
        "understood" => "Capito",
        "adults" => "adulto/i",
        "children" => "bambino/i",
        "payInstructionsDefault" => "Seleziona un metodo di pagamento per vedere le istruzioni.",
        "payTransferTitle" => "Istruzioni di Pagamento",
        "bankData" => "Dati bancari:",
        "bank" => "Banca:",
        "account" => "Conto:",
        "holder" => "Titolare:",
        "sendEmail" => "Invia la ricevuta a hotelmadisonsuite@gmail.com",
        "makeTransfer" => "Effettua il trasferimento a:",
        "sendWhatsapp" => "Invia la ricevuta via WhatsApp per confermare la prenotazione.",
    ],
];

$reservationText = $reservationI18n[$currentLang ?? "es"] ?? $reservationI18n["es"];

function reservationRoomName($name, $lang)
{
    $map = [
        "en" => [
            "Habitación Triple" => "Triple Room",
            "Habitación Matrimonial" => "Matrimonial Room",
            "Habitación Doble" => "Double Room",
            "Suite Executive" => "Executive Suite",
            "Suite Familiar - 3p" => "Family Suite - 3p",
            "Habitación Familiar - 3p" => "Family Room - 3p",
            "Familiar Plus - 4p" => "Family Plus - 4p",
        ],
        "pt" => [
            "Habitación Triple" => "Quarto Triplo",
            "Habitación Matrimonial" => "Quarto Matrimonial",
            "Habitación Doble" => "Quarto Duplo",
            "Suite Executive" => "Suíte Executiva",
            "Suite Familiar - 3p" => "Suíte Familiar - 3p",
            "Habitación Familiar - 3p" => "Quarto Familiar - 3p",
            "Familiar Plus - 4p" => "Familiar Plus - 4p",
        ],
        "it" => [
            "Habitación Triple" => "Camera Tripla",
            "Habitación Matrimonial" => "Camera Matrimoniale",
            "Habitación Doble" => "Camera Doppia",
            "Suite Executive" => "Suite Executive",
            "Suite Familiar - 3p" => "Suite Familiare - 3p",
            "Habitación Familiar - 3p" => "Camera Familiare - 3p",
            "Familiar Plus - 4p" => "Familiare Plus - 4p",
        ],
    ];

    return $map[$lang][$name] ?? $name;
}
?>

<body>

<?php include("../includes/navbar.php"); ?>

<style>
.reservation-page{background:#f7f5ef;min-height:calc(100vh - 80px);padding:38px 16px 60px}
.reservation-shell{max-width:760px;margin:0 auto}
.reservation-shell h1{color:#050505;font-size:34px;font-weight:900;margin-bottom:22px;text-align:center}
.reservation-steps{align-items:center;display:flex;justify-content:center;gap:12px;margin-bottom:30px}
.reservation-steps i{background:#c8ced8;height:2px;width:44px}
.step-dot{align-items:center;background:transparent;border:0;color:#7b8494;display:inline-flex;font-size:13px;gap:8px}
.step-dot span{align-items:center;background:#cfd5df;border-radius:50%;color:white;display:inline-flex;font-weight:800;height:32px;justify-content:center;width:32px}
.step-dot.active,.step-dot.done{color:#a66f08}.step-dot.active span,.step-dot.done span{background:#c99721}
.step-dot.done span{font-size:0}.step-dot.done span:before{content:"\2713";font-size:15px}
.reservation-card{background:white;border-radius:8px;box-shadow:0 8px 28px rgba(0,0,0,.12);padding:28px}
.reservation-step{display:none}.reservation-step.active{display:block}
.reservation-step h2{color:#050505;font-size:20px;font-weight:900;margin-bottom:20px}
.reservation-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px 22px}
.reservation-grid label,.room-picker label{display:grid;gap:7px}
.reservation-grid span,.room-picker legend{color:#3d2a0d;font-size:13px;font-weight:800}
.reservation-grid input,.reservation-grid select{border:1px solid #d3b15b;border-radius:7px;min-height:42px;padding:8px 12px;width:100%}
.room-picker{border:0;display:grid;gap:12px;margin:22px 0 0;padding:0}
.room-picker label{align-items:center;border:1px solid #d3b15b;border-radius:8px;cursor:pointer;display:flex;justify-content:space-between;padding:13px}
.room-picker label.is-selected{background:#fff7df;border-color:#b87a20;box-shadow:0 0 0 2px rgba(200,151,33,.16)}
.room-picker small{color:#6b7280;display:block;font-weight:600;margin-left:24px}.room-picker b{color:#9a680c}
.reservation-live-total{background:#f7f8fa;border-radius:8px;display:flex;justify-content:space-between;margin-top:16px;padding:13px 16px}
.reservation-live-total b,.reservation-summary b,.reservation-summary span[data-summary="total"]{color:#a66f08}
.reservation-actions{display:flex;justify-content:space-between;margin-top:26px}.reservation-actions button{border-radius:8px;font-weight:800;min-width:126px;padding:11px 20px}
.btn-step-next{background:linear-gradient(90deg,#b87a20,#f1d26a);border:1px solid #b87a20;color:#111;margin-left:auto}.btn-step-prev{background:white;border:1px solid #d3b15b;color:#111}
.reservation-summary{background:#f7f8fa;border-radius:8px;margin-top:22px;padding:16px}.reservation-summary h3{font-size:16px;font-weight:900;margin-bottom:12px}
.reservation-summary p{display:flex;justify-content:space-between;margin:7px 0}.payment-methods{display:grid;gap:14px}
.payment-methods label{align-items:center;border:1px solid #d3b15b;border-radius:8px;cursor:pointer;display:flex;gap:14px;padding:18px}
.payment-methods label.is-selected{background:#fff7df;border-color:#b87a20;box-shadow:0 0 0 2px rgba(200,151,33,.16)}
.payment-instructions{background:#fff7df;border-radius:8px;margin-top:18px;padding:16px}.payment-instructions h3{font-size:15px;font-weight:900;margin-bottom:12px}
.payment-box{background:white;border-radius:4px;margin:10px 0;padding:12px}
.availability-box{background:#fff8e4;border:1px solid #d3b15b;border-radius:8px;color:#4b3410;margin-top:16px;padding:14px 16px}
.availability-box h3{font-size:15px;font-weight:900;margin:0 0 8px}
.availability-box p{margin:0 0 7px}.availability-box ul{margin:8px 0 0;padding-left:18px}
.availability-box.is-free{background:#f7fff4;border-color:#92c479;color:#1f5b1d}
.availability-box.is-busy{background:#fff1e8;border-color:#e6a179;color:#91370d}
.reservation-modal{align-items:center;background:rgba(5,5,5,.64);display:none;inset:0;justify-content:center;padding:20px;position:fixed;z-index:3000}
.reservation-modal.is-open{display:flex}
.reservation-modal-card{background:#fff;border:2px solid #d4a51f;border-radius:14px;box-shadow:0 22px 60px rgba(0,0,0,.34);max-width:460px;overflow:hidden;text-align:center;width:100%}
.reservation-modal-head{background:#080808;color:#f1d26a;padding:22px 20px 16px}
.reservation-modal-icon{align-items:center;background:linear-gradient(135deg,#b87a20,#f1d26a);border-radius:50%;color:#111;display:inline-flex;font-size:30px;height:64px;justify-content:center;margin-bottom:10px;width:64px}
.reservation-modal-head h3{font-size:22px;font-weight:900;margin:0}
.reservation-modal-body{color:#2d2d2d;font-size:16px;line-height:1.5;padding:22px 26px}
.reservation-modal-actions{display:flex;justify-content:center;padding:0 26px 26px}
.reservation-modal-actions button{background:linear-gradient(90deg,#b87a20,#f1d26a);border:0;border-radius:10px;color:#111;font-weight:900;min-width:140px;padding:12px 22px}
@media(max-width:700px){.reservation-card{padding:22px}.reservation-grid{grid-template-columns:1fr}.reservation-steps{gap:7px}.reservation-steps i{width:24px}.room-picker label,.reservation-summary p{align-items:flex-start;flex-direction:column;gap:6px}}
</style>

<main class="reservation-page">
    <section class="reservation-shell">
        <h1>Reservar Habitaci&oacute;n</h1>

        <div class="reservation-steps" aria-label="Pasos de reserva">
            <button class="step-dot active" type="button" data-step-target="1"><span>1</span> <?php echo htmlspecialchars($reservationText["stepDates"]); ?></button>
            <i></i>
            <button class="step-dot" type="button" data-step-target="2"><span>2</span> <?php echo htmlspecialchars($reservationText["stepData"]); ?></button>
            <i></i>
            <button class="step-dot" type="button" data-step-target="3"><span>3</span> <?php echo htmlspecialchars($reservationText["stepPayment"]); ?></button>
        </div>

        <form class="reservation-card" action="guardar_reserva.php" method="POST" id="reservationForm">
            <input type="hidden" name="noches" id="nochesInput" value="1">
            <input type="hidden" name="total" id="totalInput" value="0">

            <section class="reservation-step active" data-step="1">
                <h2><?php echo htmlspecialchars($reservationText["bookingDetails"]); ?></h2>

                <div class="reservation-grid">
                    <label>
                        <span><i class="bi bi-calendar-event"></i> Fecha de Entrada</span>
                        <input type="date" name="fecha_ingreso" id="fechaIngreso" required>
                    </label>

                    <label>
                        <span><i class="bi bi-calendar-event"></i> Fecha de Salida</span>
                        <input type="date" name="fecha_salida" id="fechaSalida" required>
                    </label>

                    <label>
                        <span><i class="bi bi-person"></i> Adultos</span>
                        <select name="adultos" id="adultos" required>
                            <?php for ($i = 1; $i <= 6; $i++) { ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </label>

                    <label>
                        <span><i class="bi bi-person"></i> Ni&ntilde;os</span>
                        <select name="ninos" id="ninos" required>
                            <?php for ($i = 0; $i <= 5; $i++) { ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </label>
                </div>

                <fieldset class="room-picker">
                    <legend><?php echo htmlspecialchars($reservationText["roomType"]); ?></legend>
                    <?php if (!$habitaciones) { ?>
                        <p class="text-muted mb-0">No hay habitaciones disponibles registradas en la base de datos.</p>
                    <?php } ?>
                    <?php foreach ($habitaciones as $habitacion) { ?>
                        <?php
                        $habitacionId = (int) $habitacion["id"];
                        $precio = (float) $habitacion["precio"];
                        $capacidad = (int) ($habitacion["capacidad"] ?? 1);
                        $habitacionNombre = reservationRoomName($habitacion["nombre"], $currentLang ?? "es");
                        ?>
                        <label>
                            <span>
                                <input
                                    type="radio"
                                    name="habitacion_id"
                                    value="<?php echo $habitacionId; ?>"
                                    data-nombre="<?php echo htmlspecialchars($habitacionNombre); ?>"
                                    data-precio="<?php echo htmlspecialchars($precio); ?>"
                                    data-capacidad="<?php echo htmlspecialchars($capacidad); ?>"
                                    <?php echo $habitacionId === $idSeleccionado ? "checked" : ""; ?>
                                    required>
                                <?php echo htmlspecialchars($habitacionNombre); ?>
                                <small><?php echo $capacidad; ?> <?php echo htmlspecialchars($reservationText["people"]); ?></small>
                            </span>
                            <b>S/ <?php echo number_format($precio, 0); ?> / <?php echo htmlspecialchars($reservationText["night"]); ?></b>
                        </label>
                    <?php } ?>
                </fieldset>

                <div class="availability-box" id="availabilityBox">
                    <h3><i class="bi bi-calendar-check"></i> <?php echo htmlspecialchars($reservationText["availabilityTitle"]); ?></h3>
                    <p><?php echo htmlspecialchars($reservationText["selectRoomDates"]); ?></p>
                </div>

                <div class="reservation-live-total">
                    <span><?php echo htmlspecialchars($reservationText["nights"]); ?>: <b id="liveNights">1</b></span>
                    <span><?php echo htmlspecialchars($reservationText["total"]); ?>: <b id="liveTotal">S/ 0</b></span>
                </div>

                <div class="reservation-actions">
                    <button type="button" class="btn-step-next" data-next="2" <?php echo !$habitaciones ? "disabled" : ""; ?>>Continuar</button>
                </div>
            </section>

            <section class="reservation-step" data-step="2">
                <h2><?php echo htmlspecialchars($reservationText["personalInfo"]); ?></h2>

                <div class="reservation-grid">
                    <label>
                        <span><i class="bi bi-person"></i> Nombre</span>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($_SESSION["usuario"] ?? ""); ?>" required>
                    </label>
                    <label>
                        <span><i class="bi bi-person"></i> <?php echo htmlspecialchars($reservationText["lastName"]); ?></span>
                        <input type="text" name="apellido" required>
                    </label>
                    <label>
                        <span><i class="bi bi-envelope"></i> Email</span>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION["correo"] ?? ""); ?>" required>
                    </label>
                    <label>
                        <span><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($reservationText["phone"]); ?></span>
                        <input type="tel" name="telefono" required>
                    </label>
                    <label>
                        <span><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($reservationText["country"]); ?></span>
                        <input type="text" name="pais" required>
                    </label>
                    <label>
                        <span><i class="bi bi-geo-alt"></i> Ciudad</span>
                        <input type="text" name="ciudad" required>
                    </label>
                </div>

                <div class="reservation-summary">
                    <h3><?php echo htmlspecialchars(t("Resumen de Reserva")); ?></h3>
                    <p>Check-in: <span data-summary="fecha_ingreso">-</span></p>
                    <p>Check-out: <span data-summary="fecha_salida">-</span></p>
                    <p><?php echo htmlspecialchars($reservationText["guests"]); ?>: <span data-summary="huespedes">-</span></p>
                    <p><?php echo htmlspecialchars($reservationText["room"]); ?>: <span data-summary="habitacion">-</span></p>
                    <p><?php echo htmlspecialchars($reservationText["total"]); ?>: <span data-summary="total">S/ 0</span></p>
                </div>

                <div class="reservation-actions">
                    <button type="button" class="btn-step-prev" data-prev="1">Anterior</button>
                    <button type="button" class="btn-step-next" data-next="3">Continuar</button>
                </div>
            </section>

            <section class="reservation-step" data-step="3">
                <h2><?php echo htmlspecialchars(t("Método de Pago")); ?></h2>

                <div class="payment-methods">
                    <label>
                        <input type="radio" name="metodo_pago" value="Yape" data-payment="yape" required>
                        <span><i class="bi bi-phone"></i></span>
                        Yape
                    </label>
                    <label>
                        <input type="radio" name="metodo_pago" value="Plin" data-payment="plin" required>
                        <span><i class="bi bi-phone-vibrate"></i></span>
                        Plin
                    </label>
                    <label>
                        <input type="radio" name="metodo_pago" value="Transferencia Bancaria" data-payment="transferencia" required>
                        <span><i class="bi bi-bank"></i></span>
                        Transferencia Bancaria
                    </label>
                </div>

                <div class="payment-instructions" id="paymentInstructions">
                    <h3><i class="bi bi-credit-card"></i> <?php echo htmlspecialchars($reservationText["payTransferTitle"]); ?></h3>
                    <p><?php echo htmlspecialchars($reservationText["payInstructionsDefault"]); ?></p>
                </div>

                <div class="reservation-summary final">
                    <h3><?php echo htmlspecialchars($reservationText["finalSummary"]); ?></h3>
                    <p><span data-summary="habitacion">-</span> <b data-summary="precio">S/ 0</b></p>
                    <p><?php echo htmlspecialchars($reservationText["nights"]); ?> <b data-summary="noches">1</b></p>
                    <hr>
                    <p class="total-line"><?php echo htmlspecialchars($reservationText["totalPay"]); ?> <b data-summary="total">S/ 0</b></p>
                </div>

                <div class="reservation-actions">
                    <button type="button" class="btn-step-prev" data-prev="2">Anterior</button>
                    <button type="submit" class="btn-step-next"><?php echo htmlspecialchars($reservationText["confirmBooking"]); ?></button>
                </div>
            </section>
        </form>
    </section>
</main>

<div class="reservation-modal" id="reservationModal" role="dialog" aria-modal="true" aria-labelledby="reservationModalTitle">
    <div class="reservation-modal-card">
        <div class="reservation-modal-head">
            <div class="reservation-modal-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <h3 id="reservationModalTitle">Revisa tu reserva</h3>
        </div>
        <div class="reservation-modal-body" id="reservationModalMessage">
            <?php echo htmlspecialchars($reservationText["modalFallback"]); ?>
        </div>
        <div class="reservation-modal-actions">
            <button type="button" id="reservationModalClose"><?php echo htmlspecialchars($reservationText["understood"]); ?></button>
        </div>
    </div>
</div>

<script>
const form = document.getElementById("reservationForm");
const steps = [...document.querySelectorAll(".reservation-step")];
const stepDots = [...document.querySelectorAll(".step-dot")];
const fechaIngreso = document.getElementById("fechaIngreso");
const fechaSalida = document.getElementById("fechaSalida");
const adultos = document.getElementById("adultos");
const ninos = document.getElementById("ninos");
const nochesInput = document.getElementById("nochesInput");
const totalInput = document.getElementById("totalInput");
const liveNights = document.getElementById("liveNights");
const liveTotal = document.getElementById("liveTotal");
const paymentInstructions = document.getElementById("paymentInstructions");
const availabilityBox = document.getElementById("availabilityBox");
const reservationModal = document.getElementById("reservationModal");
const reservationModalTitle = document.getElementById("reservationModalTitle");
const reservationModalMessage = document.getElementById("reservationModalMessage");
const reservationModalClose = document.getElementById("reservationModalClose");
const roomBookings = <?php echo json_encode($ocupaciones, JSON_UNESCAPED_UNICODE); ?>;
const i18n = <?php echo json_encode($reservationText, JSON_UNESCAPED_UNICODE); ?>;

const today = new Date();
const todayText = today.toISOString().split("T")[0];
fechaIngreso.min = todayText;
fechaSalida.min = todayText;

function money(value) {
    return "S/ " + Number(value || 0).toFixed(0);
}

function selectedRoom() {
    return document.querySelector("input[name='habitacion_id']:checked");
}

function showReservationModal(title, message) {
    reservationModalTitle.textContent = title;
    reservationModalMessage.innerHTML = message;
    reservationModal.classList.add("is-open");
    reservationModalClose.focus();
}

function closeReservationModal() {
    reservationModal.classList.remove("is-open");
}

function formatDate(value) {
    if (!value) return "";
    const [year, month, day] = value.split("-");
    return `${day}/${month}/${year}`;
}

function selectedDatesOverlap(booking) {
    if (!fechaIngreso.value || !fechaSalida.value) return false;
    return fechaIngreso.value < booking.hasta && fechaSalida.value > booking.desde;
}

function updateAvailabilityBox() {
    const room = selectedRoom();
    availabilityBox.classList.remove("is-free", "is-busy");

    if (!room) {
        availabilityBox.innerHTML = `
            <h3><i class="bi bi-calendar-check"></i> ${i18n.availabilityTitle}</h3>
            <p>${i18n.selectRoom}</p>`;
        return false;
    }

    const bookings = roomBookings[room.value] || [];
    const conflicts = bookings.filter(selectedDatesOverlap);

    if (conflicts.length > 0) {
        const firstConflict = conflicts[0];
        availabilityBox.classList.add("is-busy");
        availabilityBox.innerHTML = `
            <h3><i class="bi bi-exclamation-triangle"></i> ${i18n.unavailableTitle}</h3>
            <p>${i18n.occupiedFrom} <b>${formatDate(firstConflict.desde)}</b> ${i18n.to} <b>${formatDate(firstConflict.hasta)}</b>.</p>
            <p>${i18n.availableFrom} <b>${formatDate(firstConflict.hasta)}</b>. ${i18n.chooseOtherDates}</p>`;
        return true;
    }

    if (!fechaIngreso.value || !fechaSalida.value) {
        if (bookings.length) {
            const items = bookings
                .slice(0, 3)
                .map(booking => `<li>${i18n.occupiedItem} ${formatDate(booking.desde)} ${i18n.to} ${formatDate(booking.hasta)}</li>`)
                .join("");
            availabilityBox.innerHTML = `
                <h3><i class="bi bi-calendar-check"></i> ${i18n.availabilityTitle}</h3>
                <p>${i18n.upcomingOccupied}</p>
                <ul>${items}</ul>`;
        } else {
            availabilityBox.classList.add("is-free");
            availabilityBox.innerHTML = `
                <h3><i class="bi bi-calendar-check"></i> ${i18n.availabilityTitle}</h3>
                <p>${i18n.noUpcoming}</p>`;
        }
        return false;
    }

    availabilityBox.classList.add("is-free");
    availabilityBox.innerHTML = `
        <h3><i class="bi bi-check-circle"></i> ${i18n.availableTitle}</h3>
        <p>${i18n.availableRange} <b>${formatDate(fechaIngreso.value)}</b> ${i18n.to} <b>${formatDate(fechaSalida.value)}</b>.</p>`;
    return false;
}

function calcNights() {
    if (!fechaIngreso.value || !fechaSalida.value) return 1;
    const start = new Date(fechaIngreso.value + "T00:00:00");
    const end = new Date(fechaSalida.value + "T00:00:00");
    const diff = Math.round((end - start) / 86400000);
    return Math.max(1, diff);
}

function updateSummary() {
    const room = selectedRoom();
    const nights = calcNights();
    const price = room ? Number(room.dataset.precio) : 0;
    const total = nights * price;
    const roomName = room ? room.dataset.nombre : "-";

    document.querySelectorAll(".room-picker label").forEach(label => {
        const input = label.querySelector("input[type='radio']");
        label.classList.toggle("is-selected", input && input.checked);
    });

    document.querySelectorAll(".payment-methods label").forEach(label => {
        const input = label.querySelector("input[type='radio']");
        label.classList.toggle("is-selected", input && input.checked);
    });

    nochesInput.value = nights;
    totalInput.value = total;
    liveNights.textContent = nights;
    liveTotal.textContent = money(total);

    document.querySelectorAll("[data-summary='fecha_ingreso']").forEach(el => el.textContent = fechaIngreso.value || "-");
    document.querySelectorAll("[data-summary='fecha_salida']").forEach(el => el.textContent = fechaSalida.value || "-");
    document.querySelectorAll("[data-summary='huespedes']").forEach(el => el.textContent = `${adultos.value} ${i18n.adults}, ${ninos.value} ${i18n.children}`);
    document.querySelectorAll("[data-summary='habitacion']").forEach(el => el.textContent = roomName);
    document.querySelectorAll("[data-summary='precio']").forEach(el => el.textContent = money(price));
    document.querySelectorAll("[data-summary='noches']").forEach(el => el.textContent = nights);
    document.querySelectorAll("[data-summary='total']").forEach(el => el.textContent = money(total));
    updateAvailabilityBox();
}

function goStep(stepNumber) {
    steps.forEach(step => step.classList.toggle("active", step.dataset.step === String(stepNumber)));
    stepDots.forEach((dot, index) => {
        dot.classList.toggle("active", index + 1 === stepNumber);
        dot.classList.toggle("done", index + 1 < stepNumber);
    });
    updateSummary();
    window.scrollTo({ top: 0, behavior: "smooth" });
}

function validateStep(stepNumber) {
    const fields = [...document.querySelectorAll(`.reservation-step[data-step='${stepNumber}'] input, .reservation-step[data-step='${stepNumber}'] select`)];
    return fields.every(field => field.reportValidity());
}

document.querySelectorAll("[data-next]").forEach(button => {
    button.addEventListener("click", () => {
        const currentStep = Number(button.closest(".reservation-step").dataset.step);
        if (!validateStep(currentStep)) return;
        if (currentStep === 1 && fechaIngreso.value >= fechaSalida.value) {
            showReservationModal(
                i18n.dateErrorTitle,
                i18n.dateErrorText
            );
            return;
        }
        const room = selectedRoom();
        const people = Number(adultos.value) + Number(ninos.value);
        if (room && people > Number(room.dataset.capacidad)) {
            showReservationModal(
                i18n.capacityTitle,
                i18n.capacityText
            );
            return;
        }
        if (currentStep === 1 && updateAvailabilityBox()) {
            showReservationModal(
                i18n.busyTitle,
                i18n.busyText
            );
            return;
        }
        goStep(Number(button.dataset.next));
    });
});

reservationModalClose.addEventListener("click", closeReservationModal);
reservationModal.addEventListener("click", event => {
    if (event.target === reservationModal) {
        closeReservationModal();
    }
});
document.addEventListener("keydown", event => {
    if (event.key === "Escape" && reservationModal.classList.contains("is-open")) {
        closeReservationModal();
    }
});

document.querySelectorAll("[data-prev]").forEach(button => {
    button.addEventListener("click", () => goStep(Number(button.dataset.prev)));
});

document.querySelectorAll("input, select").forEach(field => {
    field.addEventListener("change", updateSummary);
    field.addEventListener("input", updateSummary);
});

document.querySelectorAll("input[name='metodo_pago']").forEach(method => {
    method.addEventListener("change", () => {
        const type = method.dataset.payment;
        if (type === "transferencia") {
            paymentInstructions.innerHTML = `
                <h3><i class="bi bi-credit-card"></i> ${i18n.payTransferTitle}</h3>
                <p>${i18n.bankData}</p>
                <div class="payment-box">
                    <strong>${i18n.bank}</strong> BCP<br>
                    <strong>${i18n.account}</strong> 123-4567890-1-23<br>
                    <strong>CCI:</strong> 00212312345678901234<br>
                    <strong>${i18n.holder}</strong> Hotel Madison Suite
                </div>
                <small>${i18n.sendEmail}</small>`;
        } else {
            paymentInstructions.innerHTML = `
                <h3><i class="bi bi-credit-card"></i> ${i18n.payTransferTitle}</h3>
                <p>${i18n.makeTransfer}</p>
                <div class="payment-box">+51 952 123 456</div>
                <small>${i18n.sendWhatsapp}</small>`;
        }
    });
});

fechaIngreso.addEventListener("change", () => {
    fechaSalida.min = fechaIngreso.value || todayText;
});

updateSummary();
</script>

<?php include("../includes/footer.php"); ?>

</body>
</html>

