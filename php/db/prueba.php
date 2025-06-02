<?php
require_once "./db.php";
$stmt = $pdo->prepare("SELECT * FROM analisis 
            JOIN muestras ON analisis.id_muestra = muestras.id
            WHERE analisis.id_muestra = ?");
$stmt->execute(["99115f0f2e2f0be7b0fee58d41e40c05"]);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
var_dump($result);
echo "</pre>";