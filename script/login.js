
document.addEventListener("DOMContentLoaded", () => {

    const formulario = document.getElementById("form-login");
    const email = document.getElementById("email");
    const senha = document.getElementById("senha");

    if (!formulario || !email || !senha) {
        return;
    }

    function mostrarMensagem(texto) {

        let mensagem = document.getElementById("mensagem-login");

        if (!mensagem) {
            mensagem = document.createElement("div");
            mensagem.id = "mensagem-login";
            mensagem.className = "alerta-login";
            mensagem.setAttribute("role", "alert");

            const icone = document.createElement("span");
            icone.className = "icone-alerta";
            icone.textContent = "!";

            const textoMensagem = document.createElement("span");
            textoMensagem.textContent = texto;

            mensagem.appendChild(icone);
            mensagem.appendChild(textoMensagem);

            formulario.insertAdjacentElement("afterend", mensagem);
        } else {
            mensagem.querySelector("span:last-child").textContent = texto;
        }
    }

    formulario.addEventListener("submit", (event) => {

        const emailValor = email.value.trim();
        const senhaValor = senha.value;

        const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailValor || !senhaValor) {
            event.preventDefault();
            mostrarMensagem("Preencha todos os campos.");
            return;
        }

        if (!regexEmail.test(emailValor)) {
            event.preventDefault();
            mostrarMensagem("Digite um e-mail válido.");
            return;
        }

        // Se os campos estiverem corretos,
        // permite que o PHP verifique o login e as tentativas.
    });

});
