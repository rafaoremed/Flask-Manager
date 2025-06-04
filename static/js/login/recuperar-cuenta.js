import { mostrarToast } from '../utils/toast.js';

document.addEventListener("DOMContentLoaded", main);
const urlPass = "../../db/pass.php";

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
        action: 'request-token'
    }

    let errores = [];

    // Validar campos vacíos
    if (labCredentials.email === "") {
        errores.push("El correo es obligatorio.");
    } else if (!validarEmail(labCredentials.email)) {
        errores.push("El formato del correo no es válido.");
    }

    if (errores.length > 0) {
        mostrarToast(errores.join("\n"), "error");
        errores.length = 0;
        return;
    }
    /* 
    try{
        const response = await fetch(urlPass, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
                action: labCredentials.action,
                email: labCredentials.email,
                csrf_token: labCredentials.csrfToken,
                noCache: Math.random()
            }),
            cache: "no-store"
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
    } */

    $.ajax({
        type: "post",
        url: urlPass,
        data: {
            action: labCredentials.action,
            email: labCredentials.email,
            csrf_token: labCredentials.csrfToken,
            noCache: Math.random()
        },
        dataType: "text",
        success: function (response) {
            if(response == "1"){
                mostrarToast("Se le mandará un email a la dirección proporcionada.", "success");
                setTimeout(() => {
                    document.getElementById("form-credentials").classList.add("hidden");
                    document.getElementById("mensaje").classList.remove("hidden");
                }, 500);
            }else{
                mostrarToast(response || "No se ha encontrado ninguna cuenta asociada a ese correo.", "error");
            }
        }
    });
    
}

function validarEmail(email) {
    // Expresión regular básica para validar email
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}
