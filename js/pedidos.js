document.addEventListener("DOMContentLoaded", function () {
    const filtro = document.getElementById("filtroStatus");
    const pedidos = document.querySelectorAll(".pedido-item");

    if (filtro) {
        filtro.addEventListener("change", function () {
            const statusSelecionado = this.value;
            pedidos.forEach(function (pedido) {
                const status = pedido.getAttribute("data-status");
                pedido.style.display = statusSelecionado === "todos" || status === statusSelecionado ? "flex" : "none";
            });
        });
    }
});