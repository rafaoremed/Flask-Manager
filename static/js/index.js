import { mostrarToast } from "./utils/toast.js";

document.addEventListener("DOMContentLoaded", main);
const urlContacto = "../utils/contacto.php";

function main() {
  $("#contact-form").on("submit", function (e) {
    e.preventDefault();
    const $form = $(this);
    const $button = $form.find("button");
    const formData = $form.serialize();

    $.post(urlContacto, formData)
      .done(function (respuesta) {
        mostrarToast(respuesta, "success");
        $form[0].reset();
      })
      .fail(function (xhr) {
        mostrarToast("Error al enviar el mensaje.", "error");
        // alert("Error al enviar el mensaje: " + xhr.responseText);
      })
      .always(function () {
        $button.prop("disabled", false).text("Enviar mensaje");
      });
  });
}
