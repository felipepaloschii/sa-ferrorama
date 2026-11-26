
// Espera o HTML terminar de carregar antes de rodar o código,
// senão o JavaScript poderia procurar elementos que ainda não existem.

document.addEventListener("DOMContentLoaded", () => {

    // Pega os elementos da tela pelo id: o formulário e os campos de e-mail e senha.

    const formulario = document.getElementById("form-login");
    const email = document.getElementById("email");
    const senha = document.getElementById("senha");

    // Se algum dos três não foi encontrado, para o script com o "return".
    // Isso evita erro caso este arquivo seja carregado em uma página sem o formulário de login.

    if (!formulario || !email || !senha) {
        return;
    }

    // Função que mostra uma mensagem de erro abaixo do formulário.
    // Recebe o texto da mensagem como parâmetro.

    function mostrarMensagem(texto) {

        // Procura se já existe uma mensagem na tela.
        // Serve para não criar várias mensagens empilhadas a cada tentativa.

        let mensagem = document.getElementById("mensagem-login");

        // Se ainda não existe, cria a mensagem do zero.

        if (!mensagem) {

            // Cria a <div> do alerta com o id e a classe do CSS.
            // O role="alert" avisa leitores de tela que apareceu uma mensagem importante.

            mensagem = document.createElement("div");
            mensagem.id = "mensagem-login";
            mensagem.className = "alerta-login";
            mensagem.setAttribute("role", "alert");

            // Cria o ícone do alerta (o "!").

            const icone = document.createElement("span");
            icone.className = "icone-alerta";
            icone.textContent = "!";

            // Cria o <span> com o texto do erro.
            // textContent insere como texto puro (não como HTML), o que evita injeção de código.

            const textoMensagem = document.createElement("span");
            textoMensagem.textContent = texto;

            // Coloca o ícone e o texto dentro da <div> do alerta.

            mensagem.appendChild(icone);
            mensagem.appendChild(textoMensagem);

            // Insere o alerta na página, logo depois do formulário.

            formulario.insertAdjacentElement("afterend", mensagem);
        } else {

            // Se a mensagem já existe, só troca o texto.
            // "span:last-child" pega o último <span>, que é o do texto (o primeiro é o ícone).

            mensagem.querySelector("span:last-child").textContent = texto;
        }
    }

    // Fica escutando o envio do formulário.
    // A função roda antes dos dados serem enviados ao servidor.

    formulario.addEventListener("submit", (event) => {

        // Pega o que foi digitado.
        // trim() remove espaços sobrando no e-mail.
        // A senha não usa trim() porque espaços podem fazer parte dela.

        const emailValor = email.value.trim();
        const senhaValor = senha.value;

        // Expressão regular que confere o formato básico de um e-mail:
        //     - [^\s@]+ : um ou mais caracteres que não sejam espaço nem @.
        //     - @       : um @ obrigatório.
        //     - \.      : um ponto obrigatório depois do domínio.
        // Exemplo: "ana@email.com" passa, "ana@email" e "ana email.com" não passam.

        const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Se o e-mail ou a senha estiverem vazios:
        //     - preventDefault() cancela o envio do formulário.
        //     - mostra a mensagem de erro.
        //     - "return" para a função, sem rodar as outras verificações.

        if (!emailValor || !senhaValor) {
            event.preventDefault();
            mostrarMensagem("Preencha todos os campos.");
            return;
        }

        // Se o e-mail não bate com o formato da regex, cancela o envio e mostra a mensagem.
        // O "!" inverte, então: se o e-mail NÃO é válido.

        if (!regexEmail.test(emailValor)) {
            event.preventDefault();
            mostrarMensagem("Digite um e-mail válido.");
            return;
        }

        // Se os campos estiverem corretos,
        // permite que o PHP verifique o login e as tentativas.
    });

});