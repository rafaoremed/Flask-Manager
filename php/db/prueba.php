<?php
require_once "./db.php";
$stmt = $pdo->prepare("SELECT * FROM analisis 
            JOIN muestras ON analisis.id_muestra = muestras.id
            WHERE analisis.id_muestra = ?");
$stmt->execute(["6f61d849e1f750a08f62f0b57fea8bfe"]);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
var_dump($result);
echo "</pre>";