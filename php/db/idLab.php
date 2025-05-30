<?php
session_start();

try {
    $conn = new PDO("mysql:host=localhost; dbname=lab; charset=utf8", 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("select id from laboratorios where email = 'rafa.test.php.1@gmail.com'");
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);  // ← Aquí recogemos el resultado

    if ($row) {
        $_SESSION["idLab"] = $row["id"];
        $_SESSION["nombreLab"] = $row["nombre"];
        $_SESSION["emailLab"] = $row["email"];

        $jsondata[] = [
            "id" => $_SESSION["idLab"],
            "nombre" => $_SESSION["nombreLab"],
            "email" => $_SESSION["emailLab"]
        ];

        return json_encode($jsondata, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        echo "No se encontró ningún laboratorio con ese email.";
    }
} catch (PDOException $pdoEx) {
    echo "Error en la conexión: " . $pdoEx->getMessage();
} finally {
    $conn = null;
}