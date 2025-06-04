<?php
session_start();

if (!isset($_SESSION["idLab"])) {
    header("Location: ../index.php");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$logged = "true";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi cuenta — Flask Manager</title>

    <link rel="stylesheet" type="text/css" href="../../../static/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../static/css/toast.css">
    <link rel="stylesheet" type="text/css" href="../../../static/css/login.css">

    <script type="text/javascript" src="../../../static/js/utils/jquery-3.7.1.js"></script>
    <script type="text/javascript" src="../../../static/js/utils/html-components.js"></script>
    <script type="module" src="../../../static/js/login/cuenta.js"></script>

    <link rel="shortcut icon" href="../../../static/img/logo-white-square/logo-transparent-svg.svg" type="image/x-icon">
</head>
<body>
    <t-header page-links="../crud/" index-link="../" session-links="./" logged="<?php echo $logged ?>"></t-header>

    <main class="form-container">
        <h1>Mi cuenta</h1>
        <form id="form-account">
            <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="id" value="<?php echo $_SESSION['idLab']; ?>">

            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre" value="<?php echo htmlspecialchars($_SESSION['nombreLab']); ?>" required>

            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" placeholder="Correo electrónico" value="<?php echo htmlspecialchars($_SESSION['emailLab']); ?>" required>

            <label for="nueva_pass">Nueva contraseña</label>
            <input type="password" name="nueva_pass" id="nueva_pass" placeholder="Nueva contraseña">

            <label for="confirmar_pass">Confirmar nueva contraseña</label>
            <input type="password" name="confirmar_pass" id="confirmar_pass" placeholder="Confirmar nueva contraseña">

            <button type="submit" id="btn-guardar" class="btn">
                <span id="btn-text">Guardar cambios</span>
                <span id="spinner" style="display:none;" class="loader"></span>
            </button>
            <button class='btn btn-danger' id="eliminar-cuenta">Eliminar cuenta</button>
        </form>
        <div id="toast-container"></div>
    </main>

    <t-footer></t-footer>
</body>
</html>
