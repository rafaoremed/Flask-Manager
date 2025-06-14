import { mostrarToast } from "../utils/toast.js";

document.addEventListener("DOMContentLoaded", main);
const urlAnalisis = "../../db/analisis.php";
const urlLimites = "../../db/limites.php";
let limites = {};
let ultimoConteoExcedidos = 0;
let inicializando = false;

function main(){
    cargarFormulario();

    const enviado = $("#enviado").val();
    if(!enviado){
        $("#analisis-container").on("click", "#btn-guardar", updateCampos);
    }
}

function updateCampos(e) {
    e.preventDefault();

    const $form = $("#analisis-container");
    const $botonGuardar = $form.find("input[type='submit']");
    const originalTexto = $botonGuardar.val();

    // Desactivar inputs y mostrar spinner
    $form.find("input").prop("disabled", true);
    $botonGuardar.val("Guardando...");

    const data = {
        action: "update",
        csrf_token: $("#csrf_token").val(),
        id_muestra: $("#id_muestra").val(),
        coliformes: valorNumericoONull($("#coliformes").val()),
        e_coli: valorNumericoONull($("#e_coli").val()),
        ph: valorNumericoONull($("#ph").val()),
        turbidez: valorNumericoONull($("#turbidez").val()),
        color: valorNumericoONull($("#color").val()),
        conductividad: valorNumericoONull($("#conductividad").val()),
        dureza: valorNumericoONull($("#dureza").val()),
        cloro: valorNumericoONull($("#cloro").val()),
        completada: $("#ch-completada").prop("checked") ? 1 : 0
    };

    $.ajax({
        type: "POST",
        url: urlAnalisis,
        data: data,
        success: function (response) {
            if(response.success){
                mostrarToast(response.message, "success");
            }else{
                mostrarToast(response.message, "error");
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            mostrarToast(response.message || "Error al actualizar análisis", "error");
        },
        complete: function () {
            setTimeout(function(){
                // Volver a habilitar los inputs y botón
                $form.find("input").prop("disabled", false);
                $botonGuardar.val(originalTexto);
                //$botonGuardar.html('<span class="spinner"></span> Guardando...');

            }, 2500);

            window.location.href = "./vista-muestras.php";
        }
    });
}


function cargarFormulario(){
    $("#analisis-container").empty();
    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");

    $.ajax({
        type: "post",
        url: urlAnalisis,
        data: {
            noCache: Math.random(),
            csrf_token: $("#csrf_token").val(),
            id: id, 
            action: 'read'
        },
        dataType: "json",
        success: function (response) {
            if (!response || response.length === 0) {
                mostrarToast("No se encontró análisis para esta muestra", "error");
                return;
            }

            const analisis = response[0];
            const tipo = analisis.tipo_analisis;
            let html = "";

            if(tipo === "FQ"){
                html += generarCampo("ph", analisis.pH, "");
                html += generarCampo("turbidez", analisis.turbidez, "(NTU)");
                html += generarCampo("color", analisis.color, "(UH)");
                html += generarCampo("conductividad", analisis.conductividad, "(µS/cm)");
                html += generarCampo("dureza", analisis.dureza, "(mg/l)");
                html += generarCampo("cloro", analisis.cloro), "(mg/l)";
            }else if(tipo === "MICRO"){
                html += generarCampo("coliformes", analisis.coliformes, "(UFC/ml)");
                html += generarCampo("e_coli", analisis.e_coli, "(UFC/ml)");
                html += generarCampo("cloro", analisis.cloro, "(mg/l)");
            }else{
                html += generarCampo("coliformes", analisis.coliformes, "(UFC/ml)");
                html += generarCampo("e_coli", analisis.e_coli, "(UFC/ml)");
                html += generarCampo("ph", analisis.pH, "");
                html += generarCampo("turbidez", analisis.turbidez, "(NTU)");
                html += generarCampo("color", analisis.color, "(UH)");
                html += generarCampo("conductividad", analisis.conductividad, "(µS/cm)");
                html += generarCampo("dureza", analisis.dureza, "(mg/l)");
                html += generarCampo("cloro", analisis.cloro, "(mg/l)");
            }
            html += `<div class="campo-check deshabilitado" id="check-wrapper">
                        <label class="check-label">
                            <input type="checkbox" id="ch-completada" name="completada" disabled ${analisis.completada ? 'checked' : ''} />
                            <span class="check-custom"></span>
                            COMPLETADA
                        </label>
                    </div>
                    <div class="campo hidden">
                        <input type="hidden" name="id_muestra" id="id_muestra" value="${analisis.id_muestra}"/>
                        <input type="hidden" name="enviado" id="enviado" value="${analisis.enviado}"/>
                    </div>
                    <input type="submit" value="Guardar" class="btn btn-primary" id="btn-guardar">`

            $("#analisis-container").html(html);

            cargarLimitesYActivarValidacion(() => {
                inicializando = true;
                // Validar todos los campos al cargar si ya tienen valor
                $("#analisis-container input[type='number']").each(function() {
                    validarCampo(this);
                });
                inicializando = false;
                contarExcedidos(); 
            });

            if(analisis.enviado){
                $("#analisis-container input").prop("disabled", true);
                $("#analisis-container input[type='submit']").hide();
            }else{
                habilitarCheck();
                // Agregar listener para verificar campos y habilitar checkbox
                $("#analisis-container").on("input", "input[type='number']", function () {
                    habilitarCheck();
                });
            }

        },
        error: function (param) { 
            mostrarToast("Hubo un fallo al cargar el formulario", "Error");
            console.error(param)
        }
        
    });

}

function generarCampo(nombre, valor, unidades) {
    return `
        <div class="campo">
            <label for="${nombre}">${nombre.toUpperCase()} ${unidades}</label>
            <input type="number" step="any" min="0" id="${nombre}" name="${nombre}" 
            value="${valor !== null && valor !== undefined ? valor : ''}" placeholder="0">
        </div>
    `;
}

function valorNumericoONull(valor) {
    const num = parseFloat(valor);
    return isNaN(num) ? null : Math.abs(num);
}

function habilitarCheck() {
    const todosLlenos = $("#analisis-container input[type='number']").toArray().every(input => {
        const val = parseFloat(input.value);
        return !isNaN(val);
    });

    const $checkbox = $("#ch-completada");
    const $wrapper = $("#check-wrapper");

    if (todosLlenos) {
        $checkbox.prop("disabled", false);
        $wrapper.removeClass("deshabilitado");
    } else {
        $checkbox.prop("disabled", true).prop("checked", false);
        $wrapper.addClass("deshabilitado");
    }
}

function cargarLimitesYActivarValidacion(callback) {
    $.ajax({
        url: urlLimites,
        method: "GET",
        dataType: "json",
        success: function(data) {
            limites = data;

            // Añadir listener para validación en todos los campos
            $("#analisis-container").on("input", "input[type='number']", function () {
                validarCampo(this);
                habilitarCheck();
            });

            if (typeof callback === "function") {
                callback(); // Ejecutar callback tras cargar límites
            }
        },
        error: function() {
            console.error("No se pudieron cargar los límites de incidencias.");
        }
    });
}

function validarCampo(input) {
    const $input = $(input);
    const nombre = $input.attr("id");
    const valor = parseFloat($input.val());

    if (isNaN(valor)) {
        $input.removeClass("valor-excedido");
        return;
    }

    const limite = limites[nombre];

    let excede = false;
    if (typeof limite === "object" && limite.min !== undefined && limite.max !== undefined) {
        excede = valor < limite.min || valor > limite.max;
    } else if (typeof limite === "number") {
        excede = valor > limite;
    }

    $input.toggleClass("valor-excedido", excede);
    if (!inicializando) {
        contarExcedidos();
    }
}

function contarExcedidos() {
    const excedidos = $("#analisis-container .valor-excedido").length;

    if (excedidos === 0 && ultimoConteoExcedidos > 0) {
        mostrarToast("Todos los valores están dentro de los límites", "success");
    }

    if (excedidos !== ultimoConteoExcedidos) {
        ultimoConteoExcedidos = excedidos;
        if (excedidos == 1) {
            mostrarToast(`Hay ${excedidos} valor fuera de los límites`, "warning");
        }
        if (excedidos > 1) {
            mostrarToast(`Hay ${excedidos} valores fuera de los límites`, "warning");
        }
    }
}
