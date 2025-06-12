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
        codigo: document.getElementById("codigo").value.trim(),
        csrfToken: document.getElementById("csrf_token").value.trim(),
        action: "2fa"
    }

    let errores = [];

    // Validar campos vacíos
    if (labCredentials.codigo === "") {
        errores.push("El código es obligatorio.");
    } else if (!/^\d{6}$/.test(labCredentials.codigo)) {
        errores.push("El formato del código no es válido.");
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
                codigo: labCredentials.codigo,
                csrf_token: labCredentials.csrfToken,
                noCache: Math.random()
            })
        });
        const text = await response.text();

        if (response.ok && text.trim() === "1") {
            mostrarToast("Inicio de sesión exitoso", "success");
            setTimeout(() => {
                window.location.href = '../crud/vista-clientes.php';
            }, 2500);
        } else {
            mostrarToast(text || "Credenciales incorrectas.", "error");
            limpiarPass();
        }
    }catch(error){
        console.error("Error en la petición: ", error);
        // mostrarToast("No se pudo conectar con el servidor.", "error");
    }
    
}

