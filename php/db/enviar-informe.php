<?php
session_start();
require_once '../utils/csrf.php';
require_once 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once 'generar-informe.php';
require_once '../libs/phpmailer/src/Exception.php';
require_once '../libs/phpmailer/src/PHPMailer.php';
require_once '../libs/phpmailer/src/SMTP.php';

// Obtener la muestra
$id_muestra = $_POST['id'] ?? '';

if (!$id_muestra) {
    echo json_encode([
        'success' => false,
        'message' => 'No se recibió el ID de la muestra.'
    ]);
    exit;
}

// Generar PDF
$pdf_info = generarPDF($id_muestra);
if ($pdf_info === false) {
    echo json_encode([
        'success' => false,
        'message' => 'No se encontró la muestra o error al generar el PDF.'
    ]);
    exit;
}

// Extraer datos útiles:
$pdf_data           = $pdf_info['pdf_data'];
$cliente_email      = $pdf_info['cliente_email'];
$cliente_nombre     = $pdf_info['cliente_nombre'];
$laboratorio_nombre = $pdf_info['laboratorio_nombre'];
$laboratorio_email  = $pdf_info['laboratorio_email'];
$muestra_numero     = $pdf_info['muestra_numero'];
$fecha_analisis     = $pdf_info['fecha_analisis'];

// Enviar correo
try{
    $config = parse_ini_file(__DIR__ . '/../../.env');
    $mail = new PHPMailer(true);

    // --- Configuración SMTP de IONOS ---
    $mail->isSMTP();
    $mail->Host = $config['SMTP_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['SMTP_USERNAME'];
    $mail->Password = $config['SMTP_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    // --- Remitente y destinatario ---
    $mail->setFrom($config['SMTP_USERNAME'], $laboratorio_nombre);
    $mail->addAddress($cliente_email, $cliente_nombre);
    $mail->addBCC($laboratorio_email);

    // --- Asunto y cuerpo ---
    $mail->Subject = "Informe de muestra #{$muestra_numero}";
    $bodyTexto = "
        Hola {$cliente_nombre},\n\n
        Adjunto encontrarás el informe correspondiente a la muestra número {$muestra_numero}, analizada en la fecha {$fecha_analisis}.\n\n
        Quedamos a tu disposición para cualquier duda.\n\n
        Atentamente,\n
        {$laboratorio_nombre}
    ";
    $mail->Body    = $bodyTexto;
    $mail->AltBody = strip_tags($bodyTexto);

    // --- Adjuntar y enviar el PDF (desde string) ---
    $nombreArchivo = "informe_muestra_{$muestra_numero}.pdf";
    $mail->addStringAttachment($pdf_data, $nombreArchivo);
    $mail->send();

    // Marcar la muestra como enviada
    $pdo->prepare("UPDATE muestras SET enviado = 1 WHERE id = ?")
        ->execute([$id_muestra]);

    echo json_encode([
        'success' => true,
        'message' => 'Correo enviado y muestra marcada como enviada.'
    ]);
    exit;

}catch(Exception $e){
        // Si falla PHPMailer, no marcamos la muestra como enviada
    echo json_encode([
        'success' => false,
        // 'message' => "Error al enviar el correo: {$mail->ErrorInfo}"
        'message' => "Error al enviar el correo: la dirección $cliente_email no es válida."
    ]);
    exit;
}