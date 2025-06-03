import { mostrarToast } from '../utils/toast.js';

document.addEventListener("DOMContentLoaded", main);
const urlLaboratorios = "../../db/laboratorios.php";

function main() {
    $("#form-account").on("submit", function (e) {
        e.preventDefault();
        actualizarCuenta();
    });

    $("#eliminar-cuenta").on("click", function(e){
        e.preventDefault();
        eliminarCuenta();
    })
}

async function eliminarCuenta() {
    if(!confirm("¿Estás seguro de que quieres eliminar tu cuenta?")) return;

    $.ajax({
        type: "post",
        url: urlLaboratorios,
        data: {
            csrf_token: document.querySelector("input[name='csrf_token']").value,
            id: document.querySelector("input[name='id']").value,
            action: "delete"
        },
        dataType: "text",
        success: function (response) {
            console.log(response);
            mostrarToast(response, "success");
            setTimeout(() => {
                window.location.href = "./logout.php";
            }, 1500)
        },
        error: function(response){
            console.error(response)
            mostrarToast(response, "error");
        }
    });

}

async function actualizarCuenta() {
    const btn = document.getElementById("btn-guardar");
    const spinner = document.getElementById("spinner");
    const btnText = document.getElementById("btn-text");

    // Deshabilitar botón y mostrar spinner
    btn.disabled = true;
    spinner.style.display = "inline-block";
    if (btnText) btnText.style.display = "none";


    const datos = {
        nombre: document.getElementById("nombre").value.trim(),
        email: document.getElementById("email").value.trim(),
        nueva_pass: document.getElementById("nueva_pass").value.trim(),
        confirmar_pass: document.getElementById("confirmar_pass").value.trim(),
        csrfToken: document.querySelector("input[name='csrf_token']").value,
        id: document.querySelector("input[name='id']").value,
        action: "update"
    };

    let errores = [];

    if (datos.nombre === "") {
        errores.push("El nombre es obligatorio.");
    }

    if (datos.email === "") {
        errores.push("El correo es obligatorio.");
    } else if (!validarEmail(datos.email)) {
        errores.push("El formato del correo no es válido.");
    }

    if (datos.nueva_pass !== "") {
        if (!validarPasswordSegura(datos.nueva_pass)) {
            errores.push("La nueva contraseña debe tener al menos 8 caracteres, incluyendo una mayúscula, una minúscula, un número y un carácter especial.");
        }
        if (datos.nueva_pass !== datos.confirmar_pass) {
            errores.push("Las contraseñas no coinciden.");
        }
    }

    if (errores.length > 0) {
        mostrarToast(errores.join("\n"), "error");
        limpiarPass();
        btn.disabled = false;
        spinner.style.display = "none";
        if (btnText) btnText.style.display = "inline";
        return;
    }

    const payload = new URLSearchParams({
        action: datos.action,
        nombre: datos.nombre,
        email: datos.email,
        csrf_token: datos.csrfToken,
        id: datos.id
    });

    if (datos.nueva_pass !== "") {
        payload.append("pass", datos.nueva_pass);
    }

    try {
        const inicio = Date.now();
        const response = await fetch(urlLaboratorios, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: payload
        });

        const text = await response.text();

        const duracion = Date.now() - inicio;
        const tiempoMinimo = 1000;

        // Espera si fue muy rápido
        if (duracion < tiempoMinimo) {
            await new Promise(resolve => setTimeout(resolve, tiempoMinimo - duracion));
        }

        if (response.ok) {
            mostrarToast(text, "success");
            limpiarPass();
        } else {
            mostrarToast(text || "Error al actualizar la cuenta.", "error");
        }

    } catch (error) {
        console.error("Error en la petición:", error);
        mostrarToast("No se pudo conectar con el servidor.", "error");
    }finally{
        btn.disabled = false;
        spinner.style.display = "none";
        if (btnText) btnText.style.display = "inline";
    }
}

function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function validarPasswordSegura(pass) {
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    return regex.test(pass);
}

function limpiarPass() {
    document.getElementById("nueva_pass").value = "";
    document.getElementById("confirmar_pass").value = "";
}
