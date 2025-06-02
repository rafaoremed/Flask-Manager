<?php
require_once "./db.php";
$stmt = $pdo->prepare("SELECT * FROM analisis a
            JOIN muestras m ON a.id_muestra = m.id
            WHERE a.id_muestra = ?");


$stmt->execute(["f0a4fc83-ec9d-4892-9e5d-881aa4593b75"]);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
var_dump($result);
echo "</pre>";