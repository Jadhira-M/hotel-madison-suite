<?php
include_once(__DIR__ . "/lang.php");

$baseUrl = "";
$scriptDir = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"] ?? ""));

if (preg_match("#/(auth|reservas|cliente|reclamaciones)$#", $scriptDir)) {
    $baseUrl = "../";
}

$stylePath = __DIR__ . "/../assets/css/style.css";
$styleVersion = is_file($stylePath) ? filemtime($stylePath) : time();
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($currentLang ?? "es"); ?>">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Madison Suite</title>

<link rel="icon" type="image/png"
href="<?php echo $baseUrl; ?>assets/img/emblema.png">

<link rel="shortcut icon" type="image/png"
href="<?php echo $baseUrl; ?>assets/img/emblema.png">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">

<link rel="stylesheet"
href="<?php echo $baseUrl; ?>assets/css/style.css?v=<?php echo $styleVersion; ?>">

</head>
