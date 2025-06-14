<?php
session_start();
require_once '../utils/csrf.php';
require_once '../utils/validaciones.php';
require_once '../utils/generarUUID.php';
require_once '../utils/enviar-2fa.php';
require_once '../utils/cambiar-pass.php';
require_once 'db.php';

// Cambiar en el hosting por el nombre del dominio
$dominio = "localhost/Flask-Manager";
$url = "http://$dominio/php/views/login/cambio-pass.php";

$action = $_POST['action'] ?? '';

switch($action){
    case 'request-token':

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        if (!$email) {
            http_response_code(400);
            echo "Correo no válido";
            exit;
        }
        
        // Buscar al usuario
        $stmt = $pdo->prepare("SELECT id, email FROM laboratorios WHERE email = ?");
        $stmt->execute([$email]);
        $lab = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lab) {
            // http_response_code(404);
            echo "Usuario no encontrado";
            exit;
        }

        // Generar token y expiración
        $token = bin2hex(random_bytes(32)); // token seguro
        $expiracion = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Guardar token en la base de datos
        $stmt = $pdo->prepare("INSERT INTO recuperaciones (id_laboratorio, token, expiracion) VALUES (?, ?, ?)");
        $stmt->execute([$lab["id"], $token, $expiracion]);

        // Enviar email (aquí puedes usar PHPMailer o mail())
        $enlace = $url . "?token=". urlencode($token);
        cambiarPass($email, $enlace);

        echo "1";
        
        break;
    case 'update-pass':
        // Validar token y nueva contraseña
        $token = $_POST['token'] ?? '';
        $pass = $_POST['pass'] ?? '';
        $csrf = $_POST['csrf_token'] ?? '';

        if (!validarPasswordSegura($pass)) {
            http_response_code(400);
            echo "Contraseña insegura";
            exit;
        }

        // Buscar token válido
        $stmt = $pdo->prepare("SELECT * FROM recuperaciones WHERE token = ? AND expiracion > NOW() AND usado = 0");
        $stmt->execute([$token]);
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$rec) {
            http_response_code(400);
            echo "Token inválido o expirado";
            exit;
        }
        try{
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE laboratorios SET pass = ? WHERE id = ?");
            $stmt->execute([$hash, $rec["id_laboratorio"]]);
    
            // Marcar token como usado
            $stmt = $pdo->prepare("UPDATE recuperaciones SET usado = 1 WHERE id = ?");
            $stmt->execute([$rec["id"]]);
    
            echo "1"; // éxito
        }catch(PDOException $e){
            echo "2";
        }
        // Cambiar contraseña
        break;
    default:
        http_response_code(400);
        echo "Acción no válida";
        break;
}