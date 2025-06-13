import { mostrarToast } from '../utils/toast.js';

document.addEventListener("DOMContentLoaded", main);

function main(){
    $("#form-credentials").on("submit", function(e){
        e.preventDefault();
        validar();
    })

    $("#btn-registrar").on("click", function(e){
        e.preventDefault();
        window.location.href = "./registro.php";
    })
}

async function validar(){
    const labCredentials = {
        email: document.getElementById("email").value.trim(),
        pass: document.getElementById("pass").value.trim(),
        csrfToken: document.getElementById("csrf_token").value.trim(),
        action: "read"
    }

    let errores = [];

    // Validar campos vacíos
    if (labCredentials.email === "") {
        errores.push("El correo es obligatorio.");
    } else if (!validarEmail(labCredentials.email)) {
        errores.push("El formato del correo no es válido.");
    }

    if (labCredentials.pass === "") {
        errores.push("La contraseña es obligatoria.");
    }

    if (errores.length > 0) {
        mostrarToast(errores.join("\n"), "error");
        errores.length = 0;
        return;
    }

    try{
        const response = await fetch("../../db/laboratorios.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
                action: labCredentials.action,
                email: labCredentials.email,
                pass: labCredentials.pass,
                csrf_token: labCredentials.csrfToken,
                noCache: Math.random()
            })
        });
        const text = await response.text();

        if (response.ok && text.trim() === "2FA") {
            mostrarToast("Código de verificación enviado al correo electrónico", "info");
            setTimeout(() => {
                window.location.href = './verificar-codigo.php';
            }, 2500);
        } else {
            mostrarToast(text || "Credenciales incorrectas.", "error");
            limpiarPass();
        }
    }catch(error){
        console.error("Error en la petición:", error);
        mostrarToast("No se pudo conectar con el servidor.", "error");
    }
    
}

function validarEmail(email) {
    // Expresión regular básica para validar email
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function limpiarPass() {
    document.querySelectorAll("input[type='password']").forEach(p => {
        p.value = "";
    });
}
