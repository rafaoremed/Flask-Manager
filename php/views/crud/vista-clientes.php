<!DOCTYPE html>
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
?>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes — Flask Manager</title>

    <link rel="stylesheet" type="text/css" href="../../../static/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../static/css/toast.css">
    <link rel="stylesheet" type="text/css" href="../../../static/css/modal.css">
    <link rel="stylesheet" type="text/css" href="../../../static/css/clientes.css">

    <script type="text/javascript" src="../../../static/js/jquery-3.7.1.js"></script>
    <script type="text/javascript" src="../../../static/js/html-components.js"></script>
    <script type="module" src="../../../static/js/crud/clientes.js"></script>

    <link rel="shortcut icon" href="../../../static/img/logo-white-square/logo-transparent-svg.svg" type="image/x-icon">
</head>

<body>
    <t-header page-links="./" index-link="../" session-links="../login/" logged="<?php echo $logged ?>"></t-header>

    <main>
        <div class="clientes-header">
            <h1>Clientes</h1>
            <div class="search-bar">
                <form action="" class="form-search">
                    <input type="text" name="search" id="input-search" placeholder="Buscar">
                </form>
                <button id="btn-nuevo-cliente" class="btn btn-primary">+ Nuevo Cliente</button>
            </div>
        </div>

        <div id="clientes-container" class="main-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Fecha de alta</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-clientes">
        <!-- Aquí se inyectarán dinámicamente los clientes -->
                </tbody>
            </table>

            <div class="empty" style="display: none;">
                <h2>No hay clientes dados de alta</h2>
            </div>
        </div> 


        <!-- Modal de cliente (crear/editar) -->
        <div id="modal-clientes" class="modal hidden">
            <div class="modal-content">
                <span class="close-modal" id="cerrar-modal">&times;</span>
                <h2 id="modal-title">Nuevo Cliente</h2>
                <form id="form-clientes">
                    <input type="hidden" id="id-cliente">
                    <input type="hidden" name="csrf_token" id="csrf_token"
                        value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="form-group">
                        <label for="name">Nombre:</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <input type="submit" value="Guardar" class="btn btn-primary form-control">
                </form>
            </div>
        </div>
        <div id="toast-container"></div>
    </main>

    <t-footer></t-footer>
</body>

</html>