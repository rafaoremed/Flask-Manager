<?php
session_start();
require_once '../utils/csrf.php';
require_once '../utils/generarUUID.php';
require_once 'db.php';

$action = $_POST['action'] ?? '';
// En caso de no haber iniciado sesión, no te deja
if(!isset($_SESSION["idLab"])){
    $action = '';
}

switch ($action) {
    case 'create':
        $id = generarUUIDv4();
        $id_laboratorio = $_SESSION['idLab'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];

        $stmt = $pdo->prepare("SELECT * FROM clientes where id_laboratorio = ? AND email = ? ORDER BY nombre");
        $stmt->execute([$_SESSION["idLab"] , $email]);

        $emailExiste = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(sizeof($emailExiste) > 0){
            echo "Error al crear el cliente, el email que intentas agregar ya se encuentra en la base de datos.";
        }else{
            $stmt = $pdo->prepare("INSERT INTO clientes (id, id_laboratorio, nombre, email) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id, $id_laboratorio, $nombre, $email]);
            echo "Cliente creado.";
        }

        break;

    case 'read':
        $stmt = $pdo->prepare("SELECT * FROM clientes where id_laboratorio = ? ORDER BY nombre");
        $stmt->execute([$_SESSION["idLab"]]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        break;

    case 'update':
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];

        $stmt = $pdo->prepare("UPDATE clientes SET nombre = ?, email = ? WHERE id = ?");
        $stmt->execute([$nombre, $email, $id]);
        echo "Cliente actualizado.";
        break;

    case 'delete':
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        echo "Cliente eliminado.";
        break;

    case 'search':
        $term = trim($_POST['term'] ?? '');

        if ($term === '') {
            // Si no hay texto, devuelve todos los clientes
            $stmt = $pdo->prepare("SELECT id, nombre FROM clientes WHERE id_laboratorio = ?");
            $stmt->execute([$_SESSION['idLab']]);
        } else {
            // Si hay texto, filtra por nombre
            $stmt = $pdo->prepare("SELECT id, nombre FROM clientes WHERE id_laboratorio = ? AND LOWER(nombre) LIKE LOWER(?)");
            $stmt->execute([$_SESSION['idLab'], "%$term%"]);
        }

        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        echo json_encode($clientes);
        break;

    default:
        echo "Acción no válida.";
        break;
}
?>

