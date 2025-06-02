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

    <script type="text/javascript" src="../../static/js/utils/jquery-3.7.1.js"></script>
    <script ttype="text/javascript" src="../../static/js/utils/html-components.js"></script>
    <script type="module" src="../../static/js/login/login.js"></script>

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
                    Optimiza la gestión de muestras de agua en tu laboratorio con una herramienta ligera, accesible y eficaz.
                </p>
                <p>
                    Un mini LIMS diseñado para ser simple, funcional y sin complicaciones.
                </p>
                <p>
                    <a href="./crud/vista-clientes.php" class="btn btn-primary" id="btn-hero">Comenzar</a>
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

        <section class="philosophy">
            <div class="section-wrapper">
                <h2>¿Por qué Flask Manager?</h2>
                <ul class="philosophy-list">
                <li>
                    <svg class="icon-check" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M16 2L6 14 0 8l2-2 4 4L14 0z"/>
                    </svg>
                    <strong>Nació de una necesidad real:</strong>&nbsp;este proyecto surgió para resolver los dolores de cabeza diarios en un pequeño laboratorio de análisis de agua.
                </li>
                <li>
                    <svg class="icon-check" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M16 2L6 14 0 8l2-2 4 4L14 0z"/>
                    </svg>
                    <strong>Simple e intuitivo:</strong>&nbsp;está pensado para que cualquier persona pueda usarlo sin formación previa.
                </li>
                <li>
                    <svg class="icon-check" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M16 2L6 14 0 8l2-2 4 4L14 0z"/>
                    </svg>
                    <strong>Mejor que una hoja de Excel, más ligero que otros LIMS:</strong>&nbsp;olvídate de archivos sueltos, errores humanos y programas sobredimensionados.
                </li>
                <li>
                    <svg class="icon-check" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M16 2L6 14 0 8l2-2 4 4L14 0z"/>
                    </svg>
                    <strong>Open source y sin ánimo de lucro:</strong>&nbsp;es libre, gratuito y está hecho con la idea de ayudar, no de venderte una suscripción.
                </li>
                </ul>
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