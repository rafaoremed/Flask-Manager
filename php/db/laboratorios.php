<?php
session_start();
require_once '../utils/csrf.php';
require_once '../utils/validaciones.php';
require_once '../utils/generarUUID.php';
require_once 'db.php';

$action = $_POST['action'] ?? '';
$nombre = trim($_POST['nombre'] ?? '');
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

if (!$email) {
    http_response_code(400);
    echo "Correo no válido";
    exit;
}

switch ($action) {
    case 'create':
        try{
            $id = generarUUIDv4();
            $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);

            if (!validarEmail($email)) {
                http_response_code(400);
                echo "Formato de correo no válido.";
                exit;
            }

            $rawPass = $_POST['pass'] ?? '';
            if (!validarPasswordSegura($rawPass)) {
                http_response_code(400);
                echo "La contraseña no cumple los requisitos mínimos.";
                exit;
            }
            // Hashear la contraseña después de validar
            $hashedPass = password_hash($rawPass, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO laboratorios (id, nombre, email, pass) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id, $nombre, $email, $hashedPass]);
            echo "Laboratorio creado";

        }catch(PDOException $pdoEx){
            http_response_code(500);
            echo "Error al crear el laboratorio: " . $pdoEx;    // Cambiar al nombre del lab después de debuggear
        }catch (Exception $e) {
            http_response_code(400);
            echo $e->getMessage();
        }
        break;

    case 'read':
        try {
            logearse($pdo, $email);
        } catch (PDOException $e) {
            http_response_code(401);
            echo "Error al iniciar sesión: " . $e->getMessage();
        }catch (Exception $e) {
            echo $e->getMessage(); // Aquí capturas la excepción con "Credenciales incorrectas"
        }
        break;

    case 'update':
        try {
            $id = $_POST['id'];

            if (!validarEmail($email)) {
                http_response_code(400);
                echo "Formato de correo no válido.";
                exit;
            }

            if (!empty($_POST['pass'])) {
                $rawPass = $_POST['pass'];
                if (!validarPasswordSegura($rawPass)) {
                    http_response_code(400);
                    echo "La nueva contraseña no cumple los requisitos mínimos.";
                    exit;
                }

                $hashedPass = password_hash($rawPass, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("UPDATE laboratorios SET nombre = ?, email = ?, pass = ? WHERE id = ?");
                $stmt->execute([$nombre, $email, $hashedPass, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE laboratorios SET nombre = ?, email = ? WHERE id = ?");
                $stmt->execute([$nombre, $email, $id]);
            }

            $_SESSION['nombreLab'] = $nombre;
            $_SESSION['emailLab'] = $email;
            echo "Laboratorio actualizado";


            /* $stmt = $pdo->prepare("UPDATE laboratorios SET nombre = ?, email = ? WHERE id = ?");
            $stmt->execute([$nombre, $email, $id]);
            echo "Laboratorio actualizado"; */
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error al actualizar el laboratorio: " . $e->getMessage();
        }catch (Exception $e) {
            http_response_code(400);
            echo $e->getMessage();
        }
        break;

    case 'delete':
        try {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM laboratorios WHERE id = ?");
            $stmt->execute([$id]);
            echo "Laboratorio eliminado";
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error al eliminar el laboratorio: " . $e->getMessage();
        }catch (Exception $e) {
            http_response_code(400);
            echo $e->getMessage();
        }
        break;

    case 'obtenerIdLab':
        echo $_SESSION["idLab"];
        break;

    default:
        http_response_code(400);
        echo "Acción no válida.";
        break;
}

function logearse($pdo, $email){
    if (!isset($_POST['pass'])) {
        throw new Exception("Falta la contraseña.");
    }
    $pass = $_POST['pass'];
    
    $stmt = $pdo->prepare("SELECT * FROM laboratorios where email = ?");
    $stmt->execute([$email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result || !password_verify($pass, $result["pass"])) {
        throw new Exception("Credenciales incorrectas." );
    }
    
    // TODO Verificación 2FA




    // session_start();
    $_SESSION["idLab"] = $result["id"];
    $_SESSION["nombreLab"] = $result["nombre"];
    $_SESSION["emailLab"] = $result["email"];
    echo 1;
}

?>
