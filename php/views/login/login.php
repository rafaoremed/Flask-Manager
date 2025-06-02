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
    <title>Inicio de sesión — Flask Manager</title>

    <link rel="stylesheet" type="text/css" href="../../../static/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../static/css/toast.css">
    <link rel="stylesheet" type="text/css" href="../../../static/css/login.css">


    <script type="text/javascript" src="../../../static/js/utils/jquery-3.7.1.js"></script>
    <script ttype="text/javascript" src="../../../static/js/utils/html-components.js"></script>
    <script type="module" src="../../../static/js/login/login.js"></script>

    <link rel="shortcut icon" href="../../../static/img/logo-white-square/logo-transparent-svg.svg" type="image/x-icon">
</head>
<body>
    <t-header page-links="../crud/" index-link="../" session-links="./" logged="<?php echo $logged ?>"></t-header>

    <main class="form-container">
        <h1>Inicio de sesión</h1>
        <form action="" id="form-credentials">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Correo electrónico" required>
            
        
                <label for="pass">Contraseña</label>
                <input type="password" name="pass" id="pass" placeholder="Contraseña" required>
            
        
                <button type="submit" id="btn-guardar" class="btn">
                    <span id="btn-text">Iniciar sesión</span>
                    <span id="spinner" style="display:none;" class="loader"></span>
                </button>
                <button type="button" id="btn-registrar" class="btn">
                    <span id="btn-text">Registrarse</span>
                    <span id="spinner" style="display:none;" class="loader"></span>
                </button>
            
        
                <a href="./recuperar-pass.php" >¿Has olvidado tu contraseña?</a>
            
        </form>
        <div id="toast-container"></div>
    </main>

    <t-footer></t-footer>
</body>
</html>