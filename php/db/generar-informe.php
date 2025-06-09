<?php
require_once 'db.php';       
require_once '../libs/tcpdf/tcpdf.php';  

function generarPDF(int|string $id_muestra): array|false
{
    global $pdo;

    // Obtener todos los datos de la muestra (tablas laboratorio, cliente, muestra, análisis)
    $stmt = $pdo->prepare("
        SELECT 
            l.nombre         AS laboratorio_nombre,
            l.email          AS laboratorio_email,
            c.nombre         AS cliente_nombre,
            c.email          AS cliente_email,
            m.numero         AS muestra_numero,
            m.fecha          AS muestra_fecha,
            m.direccion      AS muestra_direccion,
            m.tipo_analisis  AS muestra_tipo,
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
        JOIN clientes    c ON l.id = c.id_laboratorio
        JOIN muestras    m ON c.id = m.id_cliente
        JOIN analisis    a ON m.id = a.id_muestra
        WHERE m.id = ?
    ");
    $stmt->execute([$id_muestra]);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fila) {
        return false;
    }

    // Crear el PDF en memoria
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($fila['laboratorio_nombre']);
    $pdf->SetTitle("Informe Muestra {$fila['muestra_numero']}");
    $pdf->SetMargins(15, 20, 15);
    $pdf->SetAutoPageBreak(true, 15);
    $pdf->AddPage();

    // Títulos y estilos
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, "Informe de Muestra Nº {$fila['muestra_numero']}", 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('helvetica', '', 10);

    $html = "<strong>Laboratorio:</strong> {$fila['laboratorio_nombre']} &lt;{$fila['laboratorio_email']}&gt;<br>
        <strong>Cliente:</strong> {$fila['cliente_nombre']} &lt;{$fila['cliente_email']}&gt;<br>
        <strong>Dirección:</strong> {$fila['muestra_direccion']}<br>
        <strong>Fecha de Toma:</strong> {$fila['muestra_fecha']}<br>
        <strong>Tipo de Análisis:</strong> {$fila['muestra_tipo']}<br>
        <strong>Fecha de Análisis:</strong> {$fila['fecha_analisis']}<br>
        <br><strong>Resultados:</strong><br>";

    if($fila["muestra_tipo"] === "TOTAL"){
        $resultados = "
        - Coliformes: {$fila['coliformes']} UFC/ml<br>
        - E. Coli: {$fila['e_coli']} UFC/ml<br>
        - pH: {$fila['pH']}<br>
        - Turbidez: {$fila['turbidez']} NTU<br>
        - Color: {$fila['color']}<br>
        - Conductividad: {$fila['conductividad']} µS/cm<br>
        - Dureza: {$fila['dureza']} mg/L<br>
        - Cloro: {$fila['cloro']} mg/L<br>
        - Incidencias: " . ($fila['incidencias'] ? 'Sí' : 'No') . "<br>";
    } else if($fila["muestra_tipo"] === "FQ"){
        $resultados = "
        - pH: {$fila['pH']}<br>
        - Turbidez: {$fila['turbidez']} NTU<br>
        - Color: {$fila['color']}<br>
        - Conductividad: {$fila['conductividad']} µS/cm<br>
        - Dureza: {$fila['dureza']} mg/L<br>
        - Cloro: {$fila['cloro']} mg/L<br>
        - Incidencias: " . ($fila['incidencias'] ? 'Sí' : 'No') . "<br>";
    } else{
        $resultados = "
        - Coliformes: {$fila['coliformes']} UFC/ml<br>
        - E. Coli: {$fila['e_coli']} UFC/ml<br>
        - Cloro: {$fila['cloro']} mg/L<br>
        - Incidencias: " . ($fila['incidencias'] ? 'Sí' : 'No') . "<br>";
    }

    $html .= $resultados;

    $pdf->writeHTML($html, true, false, true, false, '');

    // Devolver el PDF como string
    $pdf_data = $pdf->Output('', 'S'); // 'S' = retorna el PDF en memoria (string)

    return [
        'pdf_data'            => $pdf_data,
        'cliente_email'       => $fila['cliente_email'],
        'laboratorio_email'   => $fila['laboratorio_email'],
        'cliente_nombre'      => $fila['cliente_nombre'],
        'laboratorio_nombre'  => $fila['laboratorio_nombre'],
        'muestra_numero'      => $fila['muestra_numero'],
        'fecha_analisis'      => $fila['fecha_analisis']
    ];
}
