import { mostrarToast } from "../utils/toast.js";

document.addEventListener("DOMContentLoaded", main);
const url = "../../db/clientes.php";

function main() {
  cargarTabla();

  $("#form-clientes").submit(function (e) {
    e.preventDefault();
    guardarCliente();
  });

  $("#btn-nuevo-cliente").on("click", function () {
    limpiarFormulario();
    $("#modal-title").text("Nuevo Cliente");
    mostrarModal();
  });

  $(".table").on("click", ".modificar-cliente", modificarCliente);
  $(".table").on("click", ".eliminar-cliente", eliminarCliente);

  $("#cerrar-modal").on("click", ocultarModal);
  $(window).on("click", function (e) {
    if (e.target.id === "modal-clientes") {
      ocultarModal();
    }
  });

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

  aplicarEstiloFilasAlternas();
}

function cargarTabla() {
  $("#tabla-clientes").empty();
  $(".empty").hide();

  $.ajax({
    type: "post",
    url: url,
    data: {
      action: "read",
      csrf_token: $("#csrf_token").val().trim(),
      noCache: Math.random(),
    },
    dataType: "json",
    success: function (response) {
      if (response.length === 0) {
        $(".empty").show();
      } else {
        response.forEach((cliente) => {
          const fila = `
                        <tr class='content' idCliente='${cliente.id}'>
                            <td data-att='nombre'>${cliente.nombre}</td>
                            <td data-att='email'>${cliente.email}</td>
                            <td data-att='fecha'>${cliente.fecha_alta}</td>
                            <td><button class='modificar-cliente btn'>Editar</button>
                            <button class='eliminar-cliente btn btn-danger'>Eliminar</button></td>
                        </tr>
                    `;
          $("#tabla-clientes").append(fila);
        });
      }
      aplicarEstiloFilasAlternas();
    },
  });
}

function eliminarCliente(e) {
  if (!confirm("¿Estás seguro de que quieres eliminar este cliente? Se eliminarán todas sus muestras asociadas.")) return;

  e.preventDefault();
  const id = $(e.target).closest("tr").attr("idCliente");

  $.ajax({
    type: "post",
    url: url,
    data: {
      action: "delete",
      id,
      csrf_token: $("#csrf_token").val().trim(),
      noCache: Math.random(),
    },
    dataType: "text",
    success: function (response) {
      cargarTabla();
      mostrarToast(response, "success");
    },
  });
}

function modificarCliente(e) {
  e.preventDefault();
  const fila = $(e.target).closest("tr");
  const id = fila.attr("idCliente");
  const nombre = fila.find("td[data-att='nombre']").text();
  const email = fila.find("td[data-att='email']").text();

  $("#id-cliente").val(id);
  $("#name").val(nombre);
  $("#email").val(email);
  $("#modal-title").text("Modificar Cliente");
  mostrarModal();
}

function guardarCliente() {
  const cliente = {
    id: $("#id-cliente").val().trim(),
    nombre: $("#name").val().trim(),
    email: $("#email").val().trim(),
    csrf_token: $("#csrf_token").val().trim(),
  };

  const action = cliente.id === "" ? "create" : "update";

  const data = {
    ...cliente,
    action,
    noCache: Math.random(),
  };

  $.ajax({
    type: "post",
    url: url,
    data,
    dataType: "text",
    success: function (response) {
      if (response.toLowerCase().includes("error")) {
        mostrarToast(response, "error");
      } else {
        limpiarFormulario();
        ocultarModal();
        cargarTabla();
        mostrarToast(response, "success");
      }
    },
  });
}

function limpiarFormulario() {
  $("#form-clientes")[0].reset();
  $("#id-cliente").val("");
}

function mostrarModal() {
  $("#modal-clientes").removeClass("hidden").fadeIn(200);
}

function ocultarModal() {
  $("#modal-clientes").fadeOut(200, () => {
    $("#modal-clientes").addClass("hidden");
  });
}

function buscar() {
  const busqueda = $("#input-search").val().toLowerCase().trim();

  $(".table tr.content").each(function () {
    const nombre = $(this)
      .find("td[data-att='nombre']")
      .text()
      .toLowerCase()
      .trim();
    const email = $(this)
      .find("td[data-att='email']")
      .text()
      .toLowerCase()
      .trim();

    const coincideNombre = nombre.includes(busqueda);
    const coincideEmail = email.startsWith(busqueda);

    if (coincideNombre || coincideEmail) {
      $(this).show();
    } else {
      $(this).hide();
    }
  });

  // Si no hay filas visibles, mostrar mensaje de vacío
  const hayResultados = $(".table tr.content:visible").length > 0;
  $(".empty").toggle(!hayResultados);

  aplicarEstiloFilasAlternas();
}

function aplicarEstiloFilasAlternas() {
  $(".table tr.content:visible").each(function (index) {
    $(this).css("background-color", index % 2 === 0 ? "var(--color90)" : "");
  });
}
