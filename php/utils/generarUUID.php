<?php
function generarUUIDv4() {
    $data = random_bytes(16);
    
    // Establecer la versión a 0100 (UUIDv4)
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    // Establecer los bits 6-7 del reloj en 10
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
?>