<?php
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validarPasswordSegura($pass) {
    // Al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $pass);
}
