<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once '../libs/phpmailer/src/Exception.php';
require_once '../libs/phpmailer/src/PHPMailer.php';
require_once '../libs/phpmailer/src/SMTP.php';

function cambiarPass($destinatario, $enlace){
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
}