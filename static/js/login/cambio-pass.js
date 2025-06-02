import { mostrarToast } from '../utils/toast.js';

document.addEventListener("DOMContentLoaded", main);

function main(){
    $("#form-credentials").on("submit", function(e){
        e.preventDefault();
        validar();
    })
}

async function validar(){
    const labCredentials = {
        email: document.getElementById("email").value.trim(),
        csrfToken: document.getElementById("csrf_token").value.trim(),
        action: "recuperar-pass"
    }

    let errores = [];

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
                pass: labCredentials.pass,
                csrf_token: labCredentials.csrfToken,
                noCache: Math.random()
            })
        });
        const text = await response.text();

        if (response.ok && text.trim() === "1") {
            mostrarToast("Se le mandará un email a la dirección proporcionada.", "success");
            setTimeout(() => {
                //window.location.href = '../crud/vista-clientes.php';
                document.getElementById("form-credentials").classList.add("hidden");
                document.getElementById("mensaje").classList.remove("hidden");
            }, 500);
        } else {
            mostrarToast("No se ha encontrado ninguna cuenta asociada a ese correo.", "error");
        }
    }catch(error){
        // console.error("Error en la petición:", error);
        mostrarToast("No se pudo conectar con el servidor.", "error");
    }
    
}

function validarPasswordSegura(pass) {
    // Mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    return regex.test(pass);
}
