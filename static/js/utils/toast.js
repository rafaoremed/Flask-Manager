export function mostrarToast(mensaje, tipo = 'info') {
    const toast = document.createElement("div");
    toast.classList.add("toast");

    // Estilo según tipo
    switch (tipo) {
        case 'error':
            toast.style.backgroundColor = "#e74c3c";
            break;
        case 'success':
            toast.style.backgroundColor = "#2ecc71";
            break;
        case 'warning':
            toast.style.backgroundColor = "#f39c12";
            break;
        default:
            toast.style.backgroundColor = "#333";
    }

    toast.textContent = mensaje;

    document.getElementById("toast-container").appendChild(toast);

    // Eliminar después de la animación
    setTimeout(() => {
        toast.remove();
    }, 4000);
}
