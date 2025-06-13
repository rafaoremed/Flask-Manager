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

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muestras — Flask Manager</title>

    <link rel="stylesheet" type="text/css" href="../../../static/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../../static/css/toast.css">
    <link rel="stylesheet" type="text/css" href="../../../static/css/modal.css">
    <link rel="stylesheet" type="text/css" href="../../../static/css/clientes.css">

    <script src="../../../static/js/utils/jquery-3.7.1.js"></script>
    <!-- Librería select2 para select con búsqueda -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->
    <link rel="stylesheet" type="text/css" href="../../../static/css/select2.css">
    <script src="../../../static/js/utils/select2.js"></script>

    <script type="text/javascript" src="../../../static/js/utils/html-components.js"></script>
    <script type="module" src="../../../static/js/crud/muestras.js"></script>

    <link rel="shortcut icon" href="../../../static/img/logo-white-square/logo-transparent-svg.svg" type="image/x-icon">
</head>

<body>
    <t-header page-links="./" index-link="../" session-links="../login/" logged="<?php echo $logged ?>"></t-header>
    <main>
        <div class="clientes-header">
            <h1>Laboratorio: <?php echo $_SESSION["nombreLab"] ?></h1>
            <h2>Muestras</h2>
            <div class="search-bar">
            <form action="" class="form-search">
                <input type="text" name="search" id="input-search" placeholder="Buscar por número, nombre de cliente o dirección">
            </form>

            <div class="filtros-fecha">
                <button id="btn-fecha-actual">Mes actual</button>
                <input type="month" id="selector-mes">
                <button id="btn-filtrar-fecha">Filtrar</button>

                <!-- Filtros ocultos para enviar al backend -->
                <input type="hidden" id="filtro-mes" name="filtro-mes">
                <input type="hidden" id="filtro-anio" name="filtro-anio">
            </div>

            <button id="btn-nueva-muestra" class="btn btn-primary">+ Nueva muestra</button>
        </div>

        </div>

        <div id="muestras-container" class="main-container">
            <table class="table">
                <thead>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Dirección</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Incidencias</th>
                    <th>Acciones Muestra</th>
                    <th>Análisis</th>
                </thead>
                <tbody id="tabla-muestras"></tbody>
            </table>
            <div class="empty" style="display: none;">
                <h2>No hay muestras dadas de alta</h2>
            </div>
        </div>

        <!-- Modal de muestra (crear/editar) -->
        <div id="modal-muestras" class="modal hidden">
            <div class="modal-content">
                <span class="close-modal" id="cerrar-modal-muestra">&times;</span>
                <h2 id="modal-title">Nueva muestra</h2>
                <form id="form-muestras">
                    <input type="hidden" id="id-muestra">
                    <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="idLab" id="idLab" class="form-control" data-idLab="<?php echo $_SESSION["idLab"]; ?>">

                    <div class="form-group">
                        <label for="select-cliente">Cliente:</label>
                        <select name="cliente" id="select-cliente" class="form-control" required></select>
                    </div>
                    <div class="form-group">
                        <label for="fecha">Fecha:</label>
                        <input type="date" name="fecha" id="input-fecha" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="direccion">Dirección:</label>
                        <input type="text" name="direccion" id="input-direccion" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="tipo_analisis">Tipo de análisis:</label>
                        <select name="tipo_analisis" id="select-tipo" class="form-control" required>
                            <option value="">Seleccionar</option>
                            <option value="TOTAL">Completo</option>
                            <option value="FQ">Fisicoquímico</option>
                            <option value="MICRO">Microbiológico</option>
                        </select>
                    </div>
                    
                    <input type="submit" value="Guardar" class="btn btn-primary">
                </form>
            </div>
        </div>

        <div id="toast-container"></div>
    </main>

    <t-footer></t-footer>

</body>

</html>