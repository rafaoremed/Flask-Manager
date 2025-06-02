import { mostrarToast } from '../utils/toast.js';

document.addEventListener("DOMContentLoaded", main);

function main(){
    $("#form-credentials").on("submit", function(e){
        e.preventDefault();
        validar();
    })
}

async function validar() {
    const labCredentials = {
        nombre: document.getElementById("nombre").value.trim(),
        email: document.getElementById("email").value.trim(),
        pass: document.getElementById("pass").value.trim(),
        repass: document.getElementById("re-pass").value.trim(),
        csrfToken: document.getElementById("csrf_token").value.trim(),
        action: "create"
    };

    let errores = [];

    if (labCredentials.nombre === "") {
        errores.push("El nombre es obligatorio.");
    }

    if (labCredentials.email === "") {
        errores.push("El correo es obligatorio.");
    } else if (!validarEmail(labCredentials.email)) {
        errores.push("El formato del correo no es válido.");
    }

    if (labCredentials.pass === "") {
        errores.push("La contraseña es obligatoria.");
    } else if (!validarPasswordSegura(labCredentials.pass)) {
        errores.push("La contraseña debe tener al menos 8 caracteres, incluyendo una mayúscula, una minúscula, un número y un carácter especial.");
    }

    if (labCredentials.pass !== labCredentials.repass) {
        errores.push("Las contraseñas no coinciden.");
    }

    if (errores.length > 0) {
        mostrarToast(errores.join("\n"), "error");
        limpiarPass();
        return;
    }

    try {
        const response = await fetch("../../db/laboratorios.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
                action: labCredentials.action,
                nombre: labCredentials.nombre,
                email: labCredentials.email,
                pass: labCredentials.pass,
                csrf_token: labCredentials.csrfToken,
                noCache: Math.random()
            })
        });

        const text = await response.text();

        if (response.ok) {
            mostrarToast(text, "success");
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 500);
        } else {
            mostrarToast(text || "Ha ocurrido un error.", "error");
        }

    } catch (error) {
        console.error("Error en la petición:", error);
        mostrarToast("No se pudo conectar con el servidor.", "error");
    }
}





function validarEmail(email) {
    // Expresión regular básica para validar email
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function validarPasswordSegura(pass) {
    // Mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    return regex.test(pass);
}

function limpiarPass(){
    document.querySelectorAll("input[type='password']").forEach(p => {
        p.value = "";
    })
}

