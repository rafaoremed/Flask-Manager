<?php
session_start();

if (!isset($_SESSION['idLab'])) {
    header("Location: ../index.php");
    exit();
}

// Destruir todos los datos de sesión
$_SESSION = [];
session_destroy();

// Redirigir al inicio
header("Location: ./login.php");
exit();
