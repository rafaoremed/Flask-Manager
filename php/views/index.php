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
    <title>Inicio — Flask Manager</title>

    <link rel="stylesheet" type="text/css" href="../../static/css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../../static/css/toast.css">
    <link rel="stylesheet" type="text/css" href="../../static/css/index.css">

    <script type="text/javascript" src="../../static/js/jquery-3.7.1.js"></script>
    <script ttype="text/javascript" src="../../static/js/html-components.js"></script>
    <script type="module" src="../../static/js/login.js"></script>

    <link rel="shortcut icon" href="../../static/img/logo-white-square/logo-transparent-svg.svg" type="image/x-icon">
</head>
<body>
    <t-header 
        page-links="./crud/" 
        index-link="./"
        session-links="./login/"
        logged="<?php echo $logged ?>">
    </t-header>

    <main class="landing-main">
        <section class="hero">
            <div class="hero-wrapper">
                <h1>Flask Manager</h1>
                <p class="subtext">
                    Gestión inteligente para laboratorios de análisis de agua.
                    
                    Un mini LIMS sencillo pero potente.
                </p>
                <p>
                    <a href="./login/login.php" class="btn btn-primary" id="btn-hero">Comenzar</a>
                </p>
            </div>
        </section>

        <section class="features">
            <div class="section-wrapper">
                <h2>¿Qué puedes hacer con Flask Manager?</h2>
                <div class="feature-list">
                    <div class="feature">
                        <h3>Gestión de clientes</h3>
                        <p>Administra fácilmente tus clientes y sus ubicaciones.</p>
                    </div>
                    <div class="feature">
                        <h3>Seguimiento de muestras</h3>
                        <p>Registra, edita y filtra muestras por cliente, fecha o dirección.</p>
                    </div>
                    <div class="feature">
                        <h3>Registro de análisis</h3>
                        <p>Asocia análisis a cada muestra y registra resultados detallados.</p>
                    </div>
                    <div class="feature">
                        <h3>Informes automáticos</h3>
                        <p>Generación y envío de informes en PDF con un clic.</p>
                    </div>
                </div>
            </div>
        </section>


        <section class="contact">
            <div class="section-wrapper">    
                <h2>Contacto</h2>
                <p>¿Tienes dudas o sugerencias? Escríbenos a <a href="mailto:soporte@flaskmanager.com">soporte@flaskmanager.com</a></p>
            </div>
        </section>
    </main>

    <t-footer></t-footer>
</body>
</html>