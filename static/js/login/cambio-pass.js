import { mostrarToast } from '../utils/toast.js';

document.addEventListener("DOMContentLoaded", main);

function main(){
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');

    if (!token) {
        // Opcional: redirigir o mostrar error si no hay token
        mostrarToast("Token de recuperación no válido o ausente.", "error");
        document.getElementById('form-credentials').classList.add("hidden");
        document.getElementById('mensaje').classList.remove("hidden");
    } 

    $("#form-credentials").on("submit", function(e){
        e.preventDefault();
        validar(token);
    })
}

async function validar(token){
    const labCredentials = {
        pass: document.getElementById("pass").value.trim(),
        repass: document.getElementById("re-pass").value.trim(),
        csrfToken: document.getElementById("csrf_token").value.trim(),
        token: token,
        action: "update-pass"
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
        const response = await fetch("../../db/pass.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
                action: labCredentials.action,
                pass: labCredentials.pass,
                token: labCredentials.token,
                csrf_token: labCredentials.csrfToken,
                noCache: Math.random()
            })
        });
        const text = await response.text();

        if (response.ok && text.trim() === "1") {
            mostrarToast("Se ha cambiado la contraseña con éxito.", "success");
            setTimeout(() => {
                window.location.href = '../login/login.php';
                document.getElementById("form-credentials").classList.add("hidden");
                document.getElementById("mensaje").classList.remove("hidden");
            }, 500);
        } else if(response.ok && text.trim() === "2"){
            mostrarToast("Ha ocurrido un error y no se ha podido restablecer su contraseña.", "error");
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
