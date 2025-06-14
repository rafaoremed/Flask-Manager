<?php
session_start();
if (!isset($_SESSION["idLab"])) {
    header('Location: ../login/login.php');
    die();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$logged = isset($_SESSION["idLab"]) ? "true" : "false";

$id_muestra = $_GET['id'] ?? null;
if(!$id_muestra){
    header("Location: ./vista-muestras.php", true);
    die();
}


?>
<!DOCTYPE html>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis — Flask Manager</title>

    <link rel="stylesheet" type="text/css" href="../../../static/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../static/css/toast.css">
    <link rel="stylesheet" type="text/css" href="../../../static/css/modal.css">
    <link rel="stylesheet" type="text/css" href="../../../static/css/clientes.css">
    <link rel="stylesheet" type="text/css" href="../../../static/css/analisis.css">

    <script type="text/javascript" src="../../../static/js/utils/jquery-3.7.1.js"></script>
    <script ttype="text/javascript" src="../../../static/js/utils/html-components.js"></script>
    <script type="module" src="../../../static/js/crud/analisis.js"></script>

    <link rel="shortcut icon" href="../../../static/img/logo-white-square/logo-transparent-svg.svg" type="image/x-icon">
</head>
<body>
    <t-header page-links="./" index-link="../" session-links="../login/" logged="<?php echo $logged ?>"></t-header>

    <main>
    <div class="clientes-header">
        <h1>Laboratorio: <?php echo $_SESSION["nombreLab"] ?></h1>
        <h2>Análisis</h2>
        <h3>Número de muestra: <?php echo $_GET["numero"] ?></h3>
        <h3>Tipo de análisis: <?php echo $_GET["tipo"] ?></h3>
    </div>

        <form action="" id="form-analisis">
            <input type="hidden" value="" id="id-cliente">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div id="analisis-container" class="main-container"></div>
        </form>
        <div id="toast-container"></div>
    </main>

    <t-footer></t-footer>
</body>
</html>