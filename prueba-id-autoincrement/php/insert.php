<?php
$fecha = $_POST["fecha"];   // Output: "2025-01-21"

if($fecha == ""){
    echo "Debe seleccionar una fecha para registrar una muestra. <br/>";
}else{
    $arrFecha = explode("-", $_POST["fecha"]);   // Output: [2025, 01, 21]
    $start = substr($arrFecha[0], 2) . $arrFecha[1] . "/";  // Output: "2501/"
    try{
        $con = new PDO("mysql:host=localhost;dbname=lab", "root", "");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Selecciona el último registro que empiece por el año y mes seleccionado
        $stmt = $con->prepare("select id_muestra from muestrastest where id_muestra like '" . $start . "%' order by id_muestra desc limit 1;");
        $stmt->execute();
        
        $lastSample = 0;
        if($stmt->rowCount() > 0){
            while($row = $stmt->fetch()){
                $lastReg = (string) $row[0];
                $lastSample = substr($lastReg, 5);  // Primera cifra numérica
            }
        }
        $lastSample++;
        // Se añaden 0 hasta completar 5 cifras
        while(strlen($lastSample) < 5){
            (string) $lastSample = "0" . $lastSample;
        }
    
    (string) $id = $start . $lastSample;
    
    echo "Id: " . $id . "<br/>";
    echo "Sample number: " . $lastSample . "<br/>";
    
    $stmt = $con->prepare("insert into muestrastest values(\"$id\" , \"$fecha\");");
    $stmt->execute();
    
    echo "Insert: Ok <br/>";
    
    }catch(PDOException $pdoEx){
        echo "Error en MySQL: " . $pdoEx->getMessage();
    }catch(Exception $e){
        echo "Error en el programa: " . $e->getMessage();
    }
}

echo "<a href='../index.html'>Volver al registro</a>";
