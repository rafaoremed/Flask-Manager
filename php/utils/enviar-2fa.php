<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once '../libs/phpmailer/src/Exception.php';
require_once '../libs/phpmailer/src/PHPMailer.php';
require_once '../libs/phpmailer/src/SMTP.php';
function enviarCodigo2FA($destinatario, $codigo) {
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
        $mail->Subject = 'Tu código de verificación (2FA)';
        $mail->Body = "<h2>Tu código de verificación es:</h2> 
                        <h3>$codigo</h3> 
                        <p>Caduca en 10 minutos.</p>";

        $mail->send();
    } catch (Exception $e) {
        throw new Exception("No se pudo enviar el correo de 2FA. Error: " . $e->getMessage());
    }
}
