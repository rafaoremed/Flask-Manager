<?php
session_start();
require_once '../utils/csrf.php';
require_once '../utils/generarUUID.php';
require_once '../utils/limites.php';
require_once 'db.php';

$action = $_REQUEST["action"] ?? '';

switch ($action) {
    case 'create':
        $id_muestra = $_POST['id_muestra'];
        $coliformes = $_POST['coliformes'] ?? null;
        $e_coli = $_POST['e_coli'] ?? null;
        $ph = $_POST['ph'] ?? null;
        $turbidez = $_POST['turbidez'] ?? null;
        $color = $_POST['color'] ?? null;
        $conductividad = $_POST['conductividad'] ?? null;
        $dureza = $_POST['dureza'] ?? null;
        $cloro = $_POST['cloro'] ?? null;
        $completada = isset($_POST['completada']) ? (bool)$_POST['completada'] : false;

        $stmt = $pdo->prepare("INSERT INTO analisis (id_muestra, coliformes, e_coli, pH, turbidez, color, conductividad, dureza, cloro, completada) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_muestra, $coliformes, $e_coli, $ph, $turbidez, $color, $conductividad, $dureza, $cloro, $completada]);
        echo "Análisis creado.";
        break;

    case 'read':
        header('Content-Type: application/json');
        $stmt = $pdo->prepare("SELECT * FROM analisis 
            JOIN muestras ON analisis.id_muestra = muestras.id
            WHERE analisis.id_muestra = ?");
        $stmt->execute([$_REQUEST["id"]]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        break;

    case 'update':
        // Comprobar si está completado o enviado
        header('Content-Type: application/json');
        $stmt = $pdo->prepare("SELECT * FROM analisis 
            JOIN muestras ON analisis.id_muestra = muestras.id
            WHERE analisis.id_muestra = ?");
        $stmt->execute([$_POST["id_muestra"]]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if($result[0]["enviado"] == 1){
            echo json_encode([
                "success" => false,
                "message" => "No se pudo actualizar la muestra."
            ]);
            break;
        }

        // Recoger los datos del front
        $id_muestra = $_POST['id_muestra'];
        $coliformes = valorONull($_POST['coliformes'] ?? null);
        $e_coli = valorONull($_POST['e_coli'] ?? null);
        $ph = valorONull($_POST['ph'] ?? null);
        $turbidez = valorONull($_POST['turbidez'] ?? null);
        $color = valorONull($_POST['color'] ?? null);
        $conductividad = valorONull($_POST['conductividad'] ?? null);
        $dureza = valorONull($_POST['dureza'] ?? null);
        $cloro = valorONull($_POST['cloro'] ?? null);

        $completada = isset($_POST['completada']) ? (bool)$_POST['completada'] : false;

        // Comprobar las incidencias
        $incidencia = false;
        if ($coliformes !== null && $coliformes > $limites['coliformes']) $incidencia = true;
        if ($e_coli !== null && $e_coli > $limites['e_coli']) $incidencia = true;
        if ($ph !== null && ($ph < $limites['ph']['min'] || $ph > $limites['ph']['max'])) $incidencia = true;
        if ($turbidez !== null && $turbidez > $limites['turbidez']) $incidencia = true;
        if ($color !== null && $color > $limites['color']) $incidencia = true;
        if ($conductividad !== null && $conductividad > $limites['conductividad']) $incidencia = true;
        if ($dureza !== null && $dureza > $limites['dureza']) $incidencia = true;
        if ($cloro !== null && $cloro > $limites['cloro']) $incidencia = true;

        // Guardar actualización
        $stmt = $pdo->prepare("UPDATE analisis 
            SET coliformes=?, e_coli=?, pH=?, turbidez=?, color=?, conductividad=?, dureza=?, cloro=?, fecha_analisis=DEFAULT, completada=?, incidencias=?
            WHERE id_muestra=?");

        $stmt->execute([
            $coliformes, $e_coli, $ph, $turbidez, $color, $conductividad, $dureza, $cloro,
            $completada, $incidencia, $id_muestra
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Análisis actualizado.",
            "incidencias" => $incidencia
        ]);
        break;

    case 'delete':
        $id_muestra = $_POST['id_muestra'];
        $stmt = $pdo->prepare("DELETE FROM analisis WHERE id_muestra = ?");
        $stmt->execute([$id_muestra]);
        echo "Análisis eliminado.";
        break;

    default:
        echo "Acción no válida.";
        break;
        
    
}

// Asegurar que si el campo viene vacío del front se use null
function valorONull($valor) {
    return $valor === '' ? null : $valor;
}

?>

