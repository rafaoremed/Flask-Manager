<?php
session_start();
/* Mostrar logs */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../utils/csrf.php';
require_once '../utils/generarUUID.php';
require_once 'db.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

header('Content-Type: application/json');
switch ($action) {
    case 'create':
        try {
            $id = generarUUIDv4();  // ID UUID para la muestra
            $id_cliente = $_POST['id_cliente'];
            $direccion = $_POST['direccion'];
            $tipo_analisis = $_POST['tipo_analisis'];
            $enviado = isset($_POST['enviado']) ? 1 : 0;
            $fecha = $_POST['fecha'] ?? date('Y-m-d');

            if (!$id_cliente || !$direccion || !$tipo_analisis || !$fecha) {
                http_response_code(400);
                echo "Faltan datos obligatorios.";
                exit;
            }

            // Generar prefijo YYMM/
            $arrFecha = explode('-', $fecha);
            $yymm = substr($arrFecha[0], 2) . str_pad($arrFecha[1], 2, '0', STR_PAD_LEFT);
            $prefijo = $yymm . "/";

            // Buscar el último número de muestra dentro del laboratorio y mes
            $stmt = $pdo->prepare("
            SELECT m.numero
            FROM muestras m
            JOIN clientes c ON m.id_cliente = c.id
            WHERE c.id_laboratorio = ?
              AND m.numero LIKE ?
            ORDER BY m.numero DESC
            LIMIT 1
        ");
            $stmt->execute([$_SESSION["idLab"], $prefijo . '%']);
            $lastNumber = 0;

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $lastNumero = $row['numero'];            // Ej: "2506/00123"
                $lastNumber = (int) substr($lastNumero, 5); // Extrae "00123"
            }

            $nuevoNumero = $lastNumber + 1;
            $numeroFormateado = $prefijo . str_pad($nuevoNumero, 5, '0', STR_PAD_LEFT); // Ej: "2506/00124"

            // Transacción: muestra + análisis
            $pdo->beginTransaction();

            // Insertar muestra
            $stmt = $pdo->prepare("
            INSERT INTO muestras (id, id_cliente, numero, fecha, direccion, tipo_analisis, enviado)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
            $stmt->execute([$id, $id_cliente, $numeroFormateado, $fecha, $direccion, $tipo_analisis, $enviado]);

            // Insertar análisis en blanco asociado
            $stmt = $pdo->prepare("INSERT INTO analisis (id_muestra) VALUES (?)");
            $stmt->execute([$id]);

            $pdo->commit();

            echo json_encode(["success" => true, "numero" => $numeroFormateado]);

        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Mensaje de error"]);
        }
        break;

    case 'get':
        $id = $_POST['id'];
        $stmt = $pdo->prepare("SELECT muestras.*, clientes.nombre AS nombre_cliente FROM muestras 
                           JOIN clientes ON muestras.id_cliente = clientes.id 
                           WHERE muestras.id = ? AND clientes.id_laboratorio = ?");
        $stmt->execute([$id, $_SESSION["idLab"]]);
        $muestra = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($muestra) {
            echo json_encode(["success" => true, "muestra" => $muestra]);
        } else {
            echo json_encode(["success" => false, "message" => "Muestra no encontrada."]);
        }
        break;



    case 'read':
        $mes = $_POST['mes'] ?? date('n');
        $anio = $_POST['anio'] ?? date('Y');


        $stmt = $pdo->prepare("SELECT muestras.*, clientes.nombre, analisis.completada, analisis.incidencias FROM muestras 
                           JOIN clientes ON muestras.id_cliente = clientes.id 
                           JOIN analisis on muestras.id = analisis.id_muestra
                           WHERE clientes.id_laboratorio = ? AND MONTH(muestras.fecha) = ? AND YEAR(muestras.fecha) = ?
                           ORDER BY muestras.numero");
        $stmt->execute([$_SESSION["idLab"], $mes, $anio]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'update':
        try{
            $id = $_POST['id'];
            $direccion = $_POST['direccion'];
            $tipo_analisis = $_POST['tipo_analisis'];
            $id_cliente = $_POST['id_cliente'];
            $fecha = $_POST['fecha'];
            $enviado = isset($_POST['enviado']) ? 1 : 0;
    
            // Obtener fecha actual de la muestra
            $stmt = $pdo->prepare("SELECT fecha FROM muestras WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $old_fecha = $stmt->fetchColumn();
            $old_dt = new DateTime($old_fecha);
            $new_dt = new DateTime($fecha);
    
            $numero = null;
    
            // Solo recalcular si cambia el mes o el año
            if ($old_dt->format('Ym') !== $new_dt->format('Ym')) {
                $prefijo = $new_dt->format('ym'); // ej. "2506"
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM muestras WHERE fecha LIKE ? AND id != ?");
                $stmt->execute([$new_dt->format('Y-m') . '%', $id]);
                $count = $stmt->fetchColumn();
                $numero = sprintf('%s/%05d', $prefijo, $count + 1);
            }
    
            $sql = "UPDATE muestras SET direccion = ?, tipo_analisis = ?, id_cliente = ?, fecha = ?, enviado = ?";
            $params = [$direccion, $tipo_analisis, $id_cliente, $fecha, $enviado];
    
            if ($numero !== null) {
                $sql .= ", numero = ?";
                $params[] = $numero;
            }
    
            $sql .= " WHERE id = ?";
            $params[] = $id;
    
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
    
            // Obtener los datos actualizados
            $stmt = $pdo->prepare("SELECT m.*, c.nombre AS nombre_cliente FROM muestras m JOIN clientes c ON m.id_cliente = c.id WHERE m.id = ?");
            $stmt->execute([$id]);
            $muestraActualizada = $stmt->fetch(PDO::FETCH_ASSOC);
    
            echo json_encode([
                "success" => true,
                "message" => "Muestra actualizada.",
                "muestra" => $muestraActualizada
            ]);
            
        }catch(Exception $e){
            echo json_encode(["success" => false, "message" => "Fecha inválida: " . $e->getMessage()]);
            exit;
        }
        break;

    case 'delete':
        $id = $_POST['id'];
        $stmt = $pdo->prepare("SELECT muestras.id FROM muestras JOIN clientes ON muestras.id_cliente = clientes.id WHERE muestras.id = ? AND clientes.id_laboratorio = ?");
        $stmt->execute([$id, $_SESSION["idLab"]]);
        // Si en la consulta no hay ninguna muestra con ese id de laboratorio
        if (!$stmt->fetch()) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Acceso no autorizado."]);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM muestras WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode([
            "success" => true,
            "message" => "Muestra eliminada."
        ]);
        break;

    default:
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Acción no válida."
        ]);
        break;
}
