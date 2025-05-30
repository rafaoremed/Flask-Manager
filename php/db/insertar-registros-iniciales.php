<?php
session_start();

try {
    $conn = new PDO("mysql:host=localhost;dbname=lab;charset=utf8mb4", 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 🔐 Encriptar contraseña del laboratorio
    $password = password_hash('admin123', PASSWORD_DEFAULT);

    // 📌 Insertar laboratorio
    $stmt = $conn->prepare("INSERT INTO laboratorios (id, nombre, email, pass) VALUES (UUID(), :nombre, :email, :pass)");
    $stmt->execute([
        ':nombre' => 'Bright Falls Lab',
        ':email' => 'rafa.test.php.1@gmail.com',
        ':pass' => $password
    ]);

    // 🔍 Obtener ID del laboratorio
    $stmt = $conn->prepare("SELECT id FROM laboratorios WHERE email = :email");
    $stmt->execute([':email' => 'rafa.test.php.1@gmail.com']);
    $lab_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

    // 📌 Insertar clientes
    $stmt = $conn->prepare("INSERT INTO clientes (id, id_laboratorio, nombre, email) VALUES (UUID(), :id_laboratorio, :nombre, :email)");
    $clientes = [
        ['Ana Torres', 'ana.torres@email.com'],
        ['Carlos Méndez', 'carlos.mendez@email.com']
    ];

    foreach ($clientes as $cliente) {
        $stmt->execute([
            ':id_laboratorio' => $lab_id,
            ':nombre' => $cliente[0],
            ':email' => $cliente[1]
        ]);
    }

    // 🔍 Obtener IDs de clientes
    $stmt = $conn->prepare("SELECT id FROM clientes WHERE email = :email");
    $stmt->execute([':email' => 'ana.torres@email.com']);
    $cli1_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

    $stmt->execute([':email' => 'carlos.mendez@email.com']);
    $cli2_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

    // 📌 Insertar muestras con fecha y UUID generados desde PHP
    $stmt = $conn->prepare("
        INSERT INTO muestras (id, id_cliente, numero, fecha, direccion, tipo_analisis)
        VALUES (:id, :id_cliente, :numero, :fecha, :direccion, :tipo_analisis)
    ");

    $muestras = [
        [$cli1_id, '2505/00001', '2025-05-25', 'Av. Siempre Viva 123', 'TOTAL'],
        [$cli1_id, '2505/00002', '2025-05-25', 'Calle Luna 45', 'FQ'],
        [$cli2_id, '2505/00003', '2025-05-25', 'Calle Sol 89', 'MICRO'],
        [$cli2_id, '2505/00004', '2025-05-25', 'Calle Estrella 12', 'TOTAL']
    ];

    $id_map = [];

    foreach ($muestras as $muestra) {
        $uuid = bin2hex(random_bytes(16)); // Genera un UUID de 32 caracteres (hex)
        $stmt->execute([
            ':id' => $uuid,
            ':id_cliente' => $muestra[0],
            ':numero' => $muestra[1],
            ':fecha' => $muestra[2],
            ':direccion' => $muestra[3],
            ':tipo_analisis' => $muestra[4]
        ]);
        $id_map[$muestra[1]] = $uuid;
    }

    // 📌 Insertar análisis
    $stmt = $conn->prepare("
        INSERT INTO analisis (id_muestra, coliformes, e_coli, pH, turbidez, color, conductividad, dureza, cloro, completada)
        VALUES (:id_muestra, :coliformes, :e_coli, :ph, :turbidez, :color, :conductividad, :dureza, :cloro, :completada)
    ");

    $analisis = [
        ['2505/00001', 5, 0, 7.1, 1, 2, 120.5, 80, 0.25, true],
        ['2505/00002', 12, 1, 6.8, 2, 3, 150.0, 90, 0.30, true],
        ['2505/00003', 25, 5, 6.5, 3, 5, 98.2, 70, 0.15, true],
        ['2505/00004', 8, 0, 7.4, 1, 2, 110.0, 85, 0.20, true]
    ];

    foreach ($analisis as $a) {
        $stmt->execute([
            ':id_muestra' => $id_map[$a[0]],
            ':coliformes' => $a[1],
            ':e_coli' => $a[2],
            ':ph' => $a[3],
            ':turbidez' => $a[4],
            ':color' => $a[5],
            ':conductividad' => $a[6],
            ':dureza' => $a[7],
            ':cloro' => $a[8],
            ':completada' => $a[9]
        ]);
    }

    echo "✅ Todos los datos se insertaron correctamente.";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
} finally {
    $conn = null;
}
?>
