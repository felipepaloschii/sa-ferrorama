
document.addEventListener("DOMContentLoaded", () => {

    const formulario = document.getElementById("form-login");
    const email = document.getElementById("email");
    const senha = document.getElementById("senha");
    const mensagem = document.getElementById("mensagem");

    if (!formulario || !email || !senha) {
        return;
    }

    formulario.addEventListener("submit", (event) => {

        const emailValor = email.value.trim();
        const senhaValor = senha.value;

        mensagem.textContent = "";
        mensagem.className = "";

        const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailValor || !senhaValor) {
            event.preventDefault();

            mensagem.textContent = "Preencha todos os campos.";
            mensagem.className = "alerta-login";
            return;
        }

        if (!regexEmail.test(emailValor)) {
            event.preventDefault();

            mensagem.textContent = "Digite um e-mail válido.";
            mensagem.className = "alerta-login";
            return;
        }


    });

});
