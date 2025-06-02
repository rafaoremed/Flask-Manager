<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


require_once '../libs/phpmailer/src/Exception.php';
require_once '../libs/phpmailer/src/PHPMailer.php';
require_once '../libs/phpmailer/src/SMTP.php';

function enviarCodigo2FA($destinatario, $codigo) {
    try {
        $mail = new PHPMailer(true);
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.ionos.es';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@flaskmanager.com';
        $mail->Password = '4K$aVFjdAS!*H1';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isHTML(true);

        // Contenido del correo
        $mail->setFrom('info@flaskmanager.com', 'Flask Manager');
        $mail->addAddress($destinatario);
        $mail->isHTML(true);
        $mail->Subject = 'Tu código de verificación (2FA)';
        $mail->Body = "<h2>Tu código de verificación es:</h2> 
                        <h3>$codigo</h3> 
                        <p>Caduca en 10 minutos.</p>";

        $mail->send();
    } catch (Exception $e) {
        throw new Exception("No se pudo enviar el correo de 2FA. Error: " . $e->getMessage());
    }
}
