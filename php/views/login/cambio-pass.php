<!DOCTYPE html>
<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$logged = isset($_SESSION["idLab"]) ? "true" : "false";
?>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña — Flask Manager</title>

    <link rel="stylesheet" type="text/css" href="../../../static/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../static/css/toast.css">
    <link rel="stylesheet" type="text/css" href="../../../static/css/login.css">


    <script type="text/javascript" src="../../../static/js/utils/jquery-3.7.1.js"></script>
    <script ttype="text/javascript" src="../../../static/js/utils/html-components.js"></script>
    <script type="module" src="../../../static/js/login/recuperar-pass.js"></script>

    <link rel="shortcut icon" href="../../../static/img/logo-white-square/logo-transparent-svg.svg" type="image/x-icon">
</head>

<body>
    <t-header page-links="../crud/" index-link="../" session-links="./" logged="<?php echo $logged ?>"></t-header>

    <main class="form-container">
        <h1>Recuperación de contraseña</h1>
        <form action="" id="form-credentials">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <label for="pass">Contraseña</label>
            <input type="password" name="pass" id="pass" placeholder="Contraseña" required>

            <label for="re-pass">Confirmar Contraseña</label>
            <input type="password" name="re-pass" id="re-pass" placeholder="Confirmar Contraseña" required>


            <button type="submit" id="btn-guardar" class="btn">
                <span id="btn-text">Cambiar la contraseña</span>
                <span id="spinner" style="display:none;" class="loader"></span>
            </button>

        </form>
        <div id="mensaje" class="hidden">
            <p>
                Por favor, revise su correo electrónico para restablecer su contraseña.
            </p>
        </div>
        <div id="toast-container"></div>
    </main>

    <t-footer></t-footer>
</body>

</html>