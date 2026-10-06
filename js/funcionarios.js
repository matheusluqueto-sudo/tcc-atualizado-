const campo = document.getElementById("campoPesquisa");
const icone = document.getElementById("iconePesquisa");
const input = document.getElementById("pesquisaFuncionario");
const cards = document.querySelectorAll(".funcionario-card");

icone.addEventListener("click", (event) => {
    event.stopPropagation();
    campo.classList.add("expandido");
    input.focus();
});

campo.addEventListener("click", (event) => {
    event.stopPropagation();
});

document.addEventListener("click", () => {
    campo.classList.remove("expandido");
    input.value = "";

    cards.forEach(card => {
        card.style.display = "";
    });
});

input.addEventListener("input", () => {
    const busca = input.value.toLowerCase().trim();

    cards.forEach(card => {
        const nome = card
            .querySelector(".dados-funcionario h2")
            .textContent
            .toLowerCase();

        card.style.display = nome.includes(busca) ? "" : "none";
    });
});