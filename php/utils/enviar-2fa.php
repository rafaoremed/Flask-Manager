<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once '../libs/phpmailer/src/Exception.php';
require_once '../libs/phpmailer/src/PHPMailer.php';
require_once '../libs/phpmailer/src/SMTP.php';
$config = parse_ini_file(__DIR__ . '/../../.env');
function enviarCodigo2FA($destinatario, $nombre, $codigo) {
    // $config = parse_ini_file(__DIR__ . '/../../.env');
    global $config;
    try {
        $mail = new PHPMailer(true);
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = $config['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['SMTP_USERNAME'];
        $mail->Password = $config['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isHTML(true);

        // Contenido del correo
        $mail->setFrom($config['SMTP_FROM_EMAIL'], $config['SMTP_FROM_NAME']);
        $mail->addAddress($destinatario);
        $mail->isHTML(true);
        $mail->Subject = 'Tu código de verificación (2FA)';
        $mail->Body = "<h2 style='color: black;'>Hola $nombre, tu código de verificación es:</h2> 
                        <h1 style='color: hsl(270, 90%, 50%);'>$codigo</h1> 
                        <p style='color: black;'>El códgo caduca en 10 minutos.</p> 
                        <p style='color: black;'>Si no has intentado iniciar sesión puedes ignorar este mensaje.</p>";

        $mail->send();
    } catch (Exception $e) {
        throw new Exception("No se pudo enviar el correo de 2FA. Error: " . $e->getMessage());
    }
}

/* function cambiarPass($destinatario, $enlace){
    $config = parse_ini_file(__DIR__ . '/../../.env');
    try {
        $mail = new PHPMailer(true);
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = $config['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['SMTP_USERNAME'];
        $mail->Password = $config['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isHTML(true);

        // Contenido del correo
        $mail->setFrom($config['SMTP_FROM_EMAIL'], $config['SMTP_FROM_NAME']);
        $mail->addAddress($destinatario);
        $mail->isHTML(true);
        $mail->Subject = 'Restablecimiento de contraseña';
        $mail->Body = "<p>Haz click en el siguiente enlace para restablecer la contraseña:</p> 
                        <p>$enlace</p> 
                        <p>Caduca en 1 hora.</p>";

        $mail->send();
    } catch (Exception $e) {
        throw new Exception("No se pudo enviar el correo de recuperación de contraseña. Error: " . $e->getMessage());
    }
} */