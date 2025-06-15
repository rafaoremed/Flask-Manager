<?php
session_start();
require_once '../utils/csrf.php';
require_once '../utils/validaciones.php';
require_once '../utils/generarUUID.php';
require_once 'db.php';

$action = $_POST['action'] ?? '';
$nombre = trim($_POST['nombre'] ?? '');
$email = $_POST['email'] ?? null;

if (in_array($action, ['create', 'read', 'update'])) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    if (!$email) {
        http_response_code(400);
        echo "Correo no válido";
        exit;
    }
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

            // Comprobar que no existe ese email
            $stmt = $pdo->prepare("SELECT * FROM laboratorios WHERE email = ?");
            $stmt->execute([$email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                throw new Exception("El email ya se encuentra en la base de datos." );
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

    case '2fa':
        try{
            $id = $_SESSION["pending_2fa"] ?? $_SESSION["idLab"];
            $codigo = $_POST["codigo"] ?? '';

            $stmt = $pdo->prepare("SELECT * FROM laboratorios WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();

            if ($user && $user["codigo_2fa"] === $codigo && strtotime($user["expiracion_2fa"]) > time()) {
                // Código correcto, iniciar sesión
                $_SESSION["idLab"] = $user["id"];
                $_SESSION["nombreLab"] = $user["nombre"];
                $_SESSION["emailLab"] = $user["email"];
                unset($_SESSION["pending_2fa"]);

                // Borrar código
                $stmt = $pdo->prepare("UPDATE laboratorios SET codigo_2fa = NULL, expiracion_2fa = NULL WHERE id = ?");
                $stmt->execute([$id]);

                echo 1;
            }
        }catch(Exception $e){
            echo "El código no se corresponde con el enviado al email.";
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
    require_once '../utils/enviar-2fa.php';

    if (!isset($_POST['pass'])) {
        throw new Exception("Falta la contraseña.");
    }
    $pass = $_POST['pass'];
    
    $stmt = $pdo->prepare("SELECT * FROM laboratorios where email = ?");
    $stmt->execute([$email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$result){
        throw new Exception("No hay ninguna cuenta registrada con ese email." );
    }

    $nombre = $result["nombre"];

    if (!password_verify($pass, $result["pass"])) {
        throw new Exception("Credenciales incorrectas." );
    }

    // Generar código 2FA
    $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiracion = date("Y-m-d H:i:s", strtotime("+10 minutes"));
    $stmt = $pdo->prepare("UPDATE laboratorios SET codigo_2fa = ?, expiracion_2fa = ? WHERE id = ?");
    $stmt->execute([$codigo, $expiracion, $result["id"]]);

    // Enviar el código por correo
    enviarCodigo2FA($email, $nombre, $codigo);

    // No iniciar sesión todavía
    $_SESSION["pending_2fa"] = $result["id"];
    session_write_close();
    session_start();
    echo "2FA";
}

?>
