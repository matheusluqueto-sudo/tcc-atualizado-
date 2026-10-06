const senha = document.getElementById("senha");
const botao = document.getElementById("mostrarSenha");
const senhaConfirm = document.getElementById("senha_confirm");
const botaoConfirm = document.getElementById("mostrarSenhaConfirm");

botao.addEventListener("click", function () {
    const visivel = senha.type === "text";
    senha.type = visivel ? "password" : "text";
    this.innerHTML = visivel ? '<i class="fa-solid fa-eye"></i>' : '<i class="fa-solid fa-eye-slash"></i>';
});

botaoConfirm.addEventListener("click", function () {
    const visivel = senhaConfirm.type === "text";
    senhaConfirm.type = visivel ? "password" : "text";
    this.innerHTML = visivel ? '<i class="fa-solid fa-eye"></i>' : '<i class="fa-solid fa-eye-slash"></i>';
});