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
    <script type="module" src="../../static/js/index.js"></script>


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
                <div class="text">
                    <h1>Flask Manager</h1>
                    <p class="subtext">
                        Optimiza la gestión de muestras de agua en tu laboratorio con una herramienta ligera, accesible y eficaz.
                    </p>
                    <p>
                        Un mini LIMS diseñado para ser simple, funcional y sin complicaciones.
                    </p>
                    <p class="hero-action">
                        <a href="./crud/vista-clientes.php" class="btn btn-primary" id="btn-hero">Comenzar</a>
                    </p>
                </div>
                <aside class="section-image">
                    <img src="../../static/img/cerrar-cientifico-borroso-sosteniendo-placa-de-petri.jpg" alt="Personal de laboratorio sosteniendo placa petri" srcset="">
                </aside>
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
            <div class="section-wrapper" >
                
                <aside class="section-image">
                    <img src="../../static/img/doctor-realizando-investigaciones-medicas-en-laboratorio.jpg" alt="Personal de laboratorio usando un espectrofotómetro" srcset="">
                </aside>

                <div class="text">
                    <h2>¿Por qué Flask Manager?</h2>
                    <div class="philosophy-list">
                        <div class="list-item">
                            <p><strong>Nació de una necesidad real:</strong></p>
                            <p>Este proyecto surgió para resolver los dolores de cabeza diarios en un pequeño laboratorio de análisis de agua.</p>
                            
                        </div>
                        <div class="list-item">
                            <p><strong>Simple e intuitivo:</strong></p>
                            <p>Está pensado para que cualquier persona pueda usarlo sin formación previa.</p>
                        </div>
                        <div class="list-item">
                            <p><strong>Mejor que una hoja de Excel, más ligero que otros LIMS:</strong></p>
                            <p>Olvídate de archivos sueltos, errores humanos y programas sobredimensionados.</p>                       
                        </div>
                        <div class="list-item">
                            <p><strong>Open source y sin ánimo de lucro:</strong></p>
                            <p>Es libre, gratuito y está hecho con la idea de ayudar, no de venderte una suscripción.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <section class="contact">
            <div class="section-wrapper" id="form-contacto">    
                
                <!-- 
                <p>¿Tienes dudas o sugerencias? Escríbenos a <a href="mailto:info@flaskmanager.com">info@flaskmanager.com</a></p>
                -->
                <form id="contact-form" method="post" class="contact-form">
                    <h2>¿Tienes alguna duda? Ponte en contacto con nosotros</h2>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required>

                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" required>

                    <label for="mensaje">Mensaje</label>
                    <textarea id="mensaje" name="mensaje" rows="5" required></textarea>

                    <button type="submit" class="btn btn-primary">Enviar mensaje</button>
                </form>

            </div>
        </section>
        <div id="toast-container"></div>
    </main>

    <t-footer></t-footer>
</body>
</html>