const camposCodigo = document.querySelectorAll(".codigo-container input");
camposCodigo.forEach((campo, index) => {
    campo.addEventListener("input", () => {
        campo.value = campo.value.replace(/\D/g, "");
        if (campo.value && index < camposCodigo.length - 1) camposCodigo[index + 1].focus();
    });
    campo.addEventListener("keydown", event => {
        if (event.key === "Backspace" && !campo.value && index > 0) camposCodigo[index - 1].focus();
    });
});
const reenviarCodigo = document.getElementById("reenviarCodigo"), mensagemReenvio = document.getElementById("mensagemReenvio");
reenviarCodigo.addEventListener("click", event => {
    event.preventDefault();
    mensagemReenvio.classList.add("mostrar");
    reenviarCodigo.textContent = "Código reenviado";
});