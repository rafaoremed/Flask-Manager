<?php
require_once './db.php';
require_once '../libs/tcpdf/tcpdf.php'; // ajusta el path si usas Composer o la descargaste manualmente

// ID del laboratorio
$id_muestra = "467e5a8fb3875023e6fc1f17258dd854 ";

// Obtener los datos
$stmt = $pdo->prepare("
    SELECT 
        l.nombre AS laboratorio_nombre,
        l.email AS laboratorio_email,
        c.id AS cliente_id,
        c.nombre AS cliente_nombre,
        c.email AS cliente_email,
        c.fecha_alta AS cliente_fecha_alta,
        m.id AS muestra_id,
        m.numero AS muestra_numero,
        m.fecha AS muestra_fecha,
        m.direccion AS muestra_direccion,
        m.tipo_analisis AS muestra_tipo,
        m.enviado AS muestra_enviado,
        a.coliformes,
        a.e_coli,
        a.pH,
        a.turbidez,
        a.color,
        a.conductividad,
        a.dureza,
        a.cloro,
        a.fecha_analisis,
        a.completada,
        a.incidencias
    FROM laboratorios l
    JOIN clientes c ON l.id = c.id_laboratorio
    JOIN muestras m ON c.id = m.id_cliente
    JOIN analisis a ON m.id = a.id_muestra
    WHERE m.id = ?
");
$stmt->execute([$id_muestra]);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Laboratorio');
$pdf->SetTitle('Informe de Muestras');
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

// Agregar título
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Informe de Muestras', 0, 1, 'C');
$pdf->Ln(5);

// Estilo normal
$pdf->SetFont('helvetica', '', 10);

// Mostrar datos
foreach ($datos as $fila) {
    $pdf->SetFont('', 'B');
    $pdf->Cell(0, 10, 'Muestra Nº ' . $fila['muestra_numero'], 0, 1);
    $pdf->SetFont('');

    $pdf->writeHTML("
        <strong>Laboratorio:</strong> {$fila['laboratorio_nombre']} ({$fila['laboratorio_email']})<br>
        <strong>Cliente:</strong> {$fila['cliente_nombre']} ({$fila['cliente_email']})<br>
        <strong>Dirección:</strong> {$fila['muestra_direccion']}<br>
        <strong>Fecha de registro:</strong> {$fila['muestra_fecha']}<br>
        <strong>Tipo de Análisis:</strong> {$fila['muestra_tipo']}<br>
        <strong>Fecha de Análisis:</strong> {$fila['fecha_analisis']}<br><br>

        <strong>Resultados:</strong><br>
        Coliformes: {$fila['coliformes']}<br>
        E. Coli: {$fila['e_coli']}<br>
        pH: {$fila['pH']}<br>
        Turbidez: {$fila['turbidez']} NTU<br>
        Color: {$fila['color']}<br>
        Conductividad: {$fila['conductividad']} µS/cm<br>
        Dureza: {$fila['dureza']} mg/L<br>
        Cloro: {$fila['cloro']} mg/L<br>
        Incidencias: " . ($fila['incidencias'] ? 'Sí' : 'No') . "<br>
        Completada: " . ($fila['completada'] ? 'Sí' : 'No') . "
        <hr>
    ");
}

// Salida
$pdf->Output('informe_muestras.pdf', 'I'); // 'I' = inline, 'D' = descarga
