import { mostrarToast } from "../utils/toast.js";

document.addEventListener("DOMContentLoaded", main);
const urlClientes = "../../db/clientes.php";
const urlMuestras = "../../db/muestras.php";
const urlEnvio = "../../db/enviar-informe.php";

function main() {
    const hoy = new Date();
    $("#filtro-mes").val(hoy.getMonth() + 1); // enero = 0
    $("#filtro-anio").val(hoy.getFullYear());

    actualizarInputFecha();
    cargaInicial();
    cargarTablaMuestras();
    cargarSelectClientes();

    // Muestras modal
    document.getElementById("btn-nueva-muestra").addEventListener("click", abrirModalNuevaMuestra);
    document.getElementById("cerrar-modal-muestra").addEventListener("click", cerrarModalMuestra);
    document.getElementById("form-muestras").addEventListener("submit", guardarMuestra);

    // Acciones muestras
    $("#tabla-muestras").on("click", ".modificar-muestra", editarMuestra);
    $("#tabla-muestras").on("click", ".eliminar-muestra", eliminarMuestra);

    // Analisis
    $("#tabla-muestras").on("click", ".ver-analisis", verAnalisis);
    $("#tabla-muestras").on("click", ".enviar-muestra", enviarMuestra);
    


    // Buscar en la barra
    $(".form-search").on("input", buscar);
    // Prevenir envío del formulario al presionar Enter
    $(".form-search").on("submit", function (e) {
        e.preventDefault();
    });

    $("#input-search").on("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
        }
    });

    // Seleccionar la fecha
    $("#btn-fecha-actual").on("click", () => {
        const hoy = new Date();
        $("#filtro-mes").val(hoy.getMonth() + 1);
        $("#filtro-anio").val(hoy.getFullYear());
        $("#selector-mes").val(`${hoy.getFullYear()}-${String(hoy.getMonth() + 1).padStart(2, '0')}`);
        cargarTablaMuestras();
        });

    $("#btn-filtrar-fecha").on("click", () => {
        const [anio, mes] = $("#selector-mes").val().split("-");
        $("#filtro-mes").val(parseInt(mes));
        $("#filtro-anio").val(parseInt(anio));
        cargarTablaMuestras();
    });
}

function eliminarMuestra() {
    if (!confirm("¿Estás seguro de que quieres eliminar esta muestra?")) return;

    const fila = $(this).closest("tr");
    const id = fila.attr("idMuestra");

    $.ajax({
        type: "post",
        url: urlMuestras,
        data: {
            action: "delete",
            csrf_token: $("#csrf_token").val(),
            id: id,
            noCache: Math.random(),
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                mostrarToast(response.message || "Muestra eliminada.", "success");
                fila.fadeOut(300, function () {
                    fila.remove();
                });
            } else {
                mostrarToast(
                    response.message || "No se pudo eliminar la muestra.",
                    "error"
                );
            }
        },
        error: function (xhr, status, error) {
            mostrarToast("Error al eliminar la muestra: " + error, "error");
        },
    });
}

function editarMuestra() {
    const id = $(this).closest("tr").attr("idMuestra");

    $.ajax({
        type: "POST",
        url: urlMuestras,
        data: {
            action: "get",
            id: id,
            csrf_token: $("#csrf_token").val(),
        },
        dataType: "json",
        success: function (data) {
            if (data.success) {
                const muestra = data.muestra;
                $("#modal-title").text("Editar muestra");
                $("#id-muestra").val(muestra.id);
                $("#input-direccion").val(muestra.direccion);
                $("#input-fecha").val(muestra.fecha);
                $("#select-tipo").val(muestra.tipo_analisis);
                $("#select-cliente")
                    .append(
                        new Option(muestra.nombre_cliente, muestra.id_cliente, true, true)
                    )
                    .trigger("change");
                $("#modal-muestras").removeClass("hidden").fadeIn(200);
            } else {
                mostrarToast(data.message || "Error al cargar la muestra", "error");
            }
        },
    });
}

function cargarSelectClientes() {
    $("#select-cliente").select2({
        placeholder: "Buscar por nombre de cliente",
        ajax: {
            url: urlClientes,
            type: "POST",
            dataType: "json",
            delay: 50,
            data: function (params) {
                return {
                    action: "search",
                    term: params.term || "",
                    csrf_token: $("#csrf_token").val()
                };
            },
            processResults: function (data) {
                console.log("Clientes recibidos:", data);
                return {
                    results: data.map(function (cliente) {
                        return {
                            id: cliente.id,
                            text: cliente.nombre,
                        };
                    }),
                };
            },
            cache: true,
        },
        minimumInputLength: 0,
    });
    console.log("Carga select 2")
}

function cargaInicial(){
        $.ajax({
        type: "post",
        url: urlClientes,
        data: {
            action: "search",
            term: "",
            csrf_token: $("#csrf_token").val(),
            noCache: Math.random()
        },
        dataType: "json",
        success: function (response) {
            if(response.length === 0){
                mostrarToast("No hay clientes disponibles. Agrega al menos uno para poder crear una muestra.", "warning");
                // Desactivar el botón (ajusta el selector si es necesario)
                $("#btn-nueva-muestra").prop("disabled", true);
                $("#btn-nueva-muestra").toggleClass("inactiva");
            }else{
                $("#btn-nueva-muestra").prop("disabled", false);
            }
        }
    });
    console.log("Carga inicial")
}

function abrirModalNuevaMuestra() {
    console.log("Modal open");
    $("#form-muestras")[0].reset();
    $("#select-cliente").val(null).trigger("change"); // limpiar Select2
    $("#modal-title").text("Nueva muestra");
    $("#id-muestra").val("");
    $("#modal-muestras").removeClass("hidden").fadeIn(200);
}

function cerrarModalMuestra() {
    console.log("Modal closed");
    $("#modal-muestras").fadeOut(200, () => {
        $("#modal-muestras").addClass("hidden");
    });
}

function guardarMuestra(e) {
    e.preventDefault();

    const idMuestra = $("#id-muestra").val().trim();
    const action = idMuestra ? "update" : "create";

    const fecha = $("#input-fecha").val();
    const direccion = $("#input-direccion").val();
    const tipoAnalisis = $("#select-tipo").val();
    const idCliente = $("#select-cliente").val();

    // Validación mínima
    if (!fecha || !direccion || !tipoAnalisis || !idCliente) {
        mostrarToast("Por favor completa todos los campos obligatorios.", "error");
        return;
    }

    // Deshabilitar botón y mostrar spinner
    const $btnGuardar = $("#btn-guardar-muestra");
    const textoOriginal = $btnGuardar.text();
    $btnGuardar.prop("disabled", true).html("Guardando...");

    $.ajax({
        type: "post",
        url: urlMuestras,
        data: {
            noCache: Math.random(),
            csrf_token: $("#csrf_token").val(),
            action: action,
            id: idMuestra,
            direccion: direccion,
            tipo_analisis: tipoAnalisis,
            fecha: fecha,
            id_cliente: idCliente,
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                mostrarToast("Muestra guardada correctamente", "success");
                cerrarModalMuestra();

                if (idMuestra) {
                    actualizarFilaMuestra(response.muestra);
                } else {
                    cargarTablaMuestras();
                }
            } else {
                mostrarToast(response.message || "No se pudo guardar la muestra", "error");
            }
        },
        error: function (xhr, status, error) {
            mostrarToast("Error de conexión: " + error, "error");
        },
        complete: function () {
            $btnGuardar.prop("disabled", false).text(textoOriginal);
        }
    });
}

function cargarTablaMuestras() {
    $("#tabla-muestras").empty();
    $(".empty").hide();

    $.ajax({
        type: "post",
        url: urlMuestras,
        data: {
            action: "read",
            csrf_token: $("#csrf_token").val().trim(),
            noCache: Math.random(),
            mes: $("#filtro-mes").val(),
            anio: $("#filtro-anio").val()
        },
        dataType: "json",
        success: function (response) {
            if (response.length === 0) {
                $(".empty").show();
            } else {
                response.forEach((muestra) => {
                    let estadoTexto = "Pendiente";
                    let claseEstado = "estado-pendiente";
                    let editable = true;
                    let enviable = false;
                    let incidencias = false;
                    let enviada = muestra.enviado;
                    

                    if(muestra.incidencias == 1 || muestra.incidencias == "true"){
                        incidencias = true;
                    }

                    if(muestra.completada){
                        estadoTexto = "Completada";
                        claseEstado = "estado-completada"

                        if (!enviada) {
                            enviable = true;
                        }
                    }

                    if(enviada){
                        console.log("Muestra " + muestra.numero + " enviada")
                        estadoTexto = "Enviada";
                        claseEstado = incidencias ? "estado-incidencias" : "estado-enviada";
                        editable = false;
                        enviable = false;
                    }

                    const txtIncidencias = incidencias ? "Si" : "No";

                    const fila = `
                        <tr class='content ${claseEstado}' idMuestra='${muestra.id}'>
                        <td data-att='numero'>${muestra.numero}</td>
                        <td data-att='cliente'>${muestra.nombre}</td>
                        <td data-att='direccion'>${muestra.direccion}</td>
                        <td data-att='tipo'>${muestra.tipo_analisis}</td>
                        <td data-att='fecha'>${muestra.fecha}</td>
                        <td data-att='estado'>${estadoTexto}</td>
                        <td data-att='incidencias'>${txtIncidencias}</td>
                        <td>
                            <button class='modificar-muestra btn'>Editar muestra</button>
                            <button class='eliminar-muestra btn btn-danger'>Eliminar muestra</button>
                            
                        </td>
                        <td>
                            <button class="btn btn-primary ver-analisis">Ver análisis</button>
                            <button class='enviar-muestra btn btn-primary' ${enviable ? "" : "hidden"}>Enviar análisis</button>
                            
                        </td>
                        </tr>
                    `;
                    $("#tabla-muestras").append(fila);
                });
            }
        },
    });
}

function verAnalisis(){
    const urlAnalisis = "./vista-analisis.php";

    const fila = $(this).closest("tr");
    const idMuestra = fila.attr("idMuestra");
    const numero = fila.find("td[data-att='numero']").text().trim();
    const tipo = fila.find("td[data-att='tipo']").text().trim();
    // console.log(urlAnalisis + "?id=" + idMuestra + "&numero=" + numero + "&tipo=" + tipo)
    window.location.href = urlAnalisis + "?id=" + idMuestra + "&numero=" + numero + "&tipo=" + tipo;
}


function actualizarFilaMuestra(muestra) {
    const fila = $(`tr[idMuestra='${muestra.id}']`);
    const txtIncidencias = muestra.incidencias == 1 || muestra.incidencias === "true" ? "Si" : "No";

    let estadoTexto = "Pendiente";
    let claseEstado = "estado-pendiente";

    if (muestra.completada == 1 || muestra.completada === true || muestra.completada === "1" || muestra.completada === "true") {
        estadoTexto = "Completada";
        claseEstado = "estado-completada";
    }

    if (muestra.estado == 1 || muestra.estado === "enviada") {
        estadoTexto = "Enviada";
        claseEstado = muestra.incidencias == 1 || muestra.incidencias === "true" ? "estado-incidencias" : "estado-enviada";
    }

    fila.find("td[data-att='numero']").text(muestra.numero);
    fila.find("td[data-att='cliente']").text(muestra.nombre_cliente);
    fila.find("td[data-att='direccion']").text(muestra.direccion);
    fila.find("td[data-att='tipo']").text(muestra.tipo_analisis);
    fila.find("td[data-att='fecha']").text(muestra.fecha);
    fila.find("td[data-att='estado']").text(estadoTexto);
    fila.find("td[data-att='incidencias']").text(txtIncidencias);

    fila.removeClass("estado-enviada estado-pendiente estado-completada estado-incidencias");
    fila.addClass(claseEstado);
}

function buscar() {
  const busqueda = $("#input-search").val().toLowerCase().trim();

  $(".table tr.content").each(function () {
    const cliente = $(this)
      .find("td[data-att='cliente']")
      .text()
      .toLowerCase()
      .trim();
    const numero = $(this)
      .find("td[data-att='numero']")
      .text()
      .toLowerCase()
      .trim();

    const direccion = $(this)
      .find("td[data-att='direccion']")
      .text()
      .toLowerCase()
      .trim();

    const coincideCliente = cliente.includes(busqueda);
    const coincideNumero = numero.includes(busqueda);
    const coincideDireccion = direccion.includes(busqueda);


    if (coincideCliente || coincideNumero || coincideDireccion) {
      $(this).show();
    } else {
      $(this).hide();
    }
  });

  // Si no hay filas visibles, mostrar mensaje de vacío
  const hayResultados = $(".table tr.content:visible").length > 0;
  $(".empty").toggle(!hayResultados);

}


function enviarMuestra(){
    const fila = $(this).closest("tr");
    const idMuestra = fila.attr("idMuestra");
    const estado = fila.find("td[data-att='estado']").text().trim();

    if (estado === "Enviada") {
        mostrarToast("Esta muestra ya ha sido enviada.", "info");
        return;
    }

    // Confirmación opcional
    if (!confirm("¿Deseas generar el informe en PDF y enviar el análisis?")) return;

    // Deshabilitar botón y mostrar spinner
    const boton = $(this);
    const textoOriginal = boton.text();
    boton.prop("disabled", true).text("Enviando...");

    $.ajax({
        type: "POST",
        url: urlEnvio, 
        data: {
            id: idMuestra,
            csrf_token: $("#csrf_token").val(),
            noCache: Math.random()
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                mostrarToast("Análisis enviado correctamente.", "success");
                cargarTablaMuestras(); // Refresca la tabla para reflejar estado
            } else {
                mostrarToast(response.message || "No se pudo enviar el análisis.", "error");
            }
        },
        error: function (xhr, status, error) {
            mostrarToast("Error al enviar el análisis: " + error, "error");
        },
        complete: function () {
            boton.prop("disabled", false).text(textoOriginal);
        }
    });
}

function actualizarInputFecha(){
    const inputMes = document.getElementById('selector-mes');
    const hoy = new Date();
    const mesActual = hoy.toISOString().slice(0, 7); // Formato "YYYY-MM"
    inputMes.value = mesActual;
}