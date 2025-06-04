<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$logged = isset($_SESSION["idLab"]) ? "true" : "false";
?>
<!DOCTYPE html>
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
    <script type="module" src="../../../static/js/login/recuperar-cuenta.js"></script>

    <link rel="shortcut icon" href="../../../static/img/logo-white-square/logo-transparent-svg.svg" type="image/x-icon">
</head>
<body>
    <t-header page-links="../crud/" index-link="../" session-links="./" logged="<?php echo $logged ?>"></t-header>

    <main class="form-container">
        <h1>Recuperación de contraseña</h1>
        <form action="" id="form-credentials">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Correo electrónico" required>
        
                <button type="submit" id="btn-guardar" class="btn">
                    <span id="btn-text">Enviar mensaje al correo</span>
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