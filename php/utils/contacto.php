<?php
session_start();
require_once 'csrf.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once '../libs/phpmailer/src/Exception.php';
require_once '../libs/phpmailer/src/PHPMailer.php';
require_once '../libs/phpmailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = htmlspecialchars(trim($_POST["nombre"]));
    $email = filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL);
    $mensaje = htmlspecialchars(trim($_POST["mensaje"]));

    if (!$email || empty($nombre) || empty($mensaje)) {
        echo "Por favor, completa todos los campos correctamente.";
        exit;
    }

    // Añadir los mensajes en un log
    $log = date("Y-m-d H:i:s") . " | $nombre <$email>: $mensaje\n";
    file_put_contents("mensajes-contacto.txt", $log, FILE_APPEND);

    $mail = new PHPMailer(true);

    try {
        $config = parse_ini_file(__DIR__ . '/../../.env');
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = $config['SMTP_HOST'];    // Ej: smtp.gmail.com
        $mail->SMTPAuth = true;
        $mail->Username = $config['SMTP_USERNAME'];
        $mail->Password = $config['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isHTML(true);

        // Remitente y destinatario
        $mail->setFrom($config['SMTP_USERNAME'], $email);
        $mail->addAddress($config['SMTP_USERNAME']); // Donde recibes el mensaje

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = "Nuevo mensaje de $nombre <$email>:";
        $mail->Body = $mensaje;

        $mail->send();
        echo "Mensaje enviado correctamente. ¡Gracias!";
    } catch (Exception $e) {
        http_response_code(500);
        echo "Error al enviar el mensaje: {$mail->ErrorInfo}";
    }
}
?>
