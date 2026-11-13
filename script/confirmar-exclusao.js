
document.addEventListener("DOMContentLoaded", () => {
    function criarPopup(titulo, mensagem, formulario = null) {
        const overlay = document.createElement("div");
        overlay.className = "popup-exclusao-overlay";

        const conteudo = document.createElement("div");
        conteudo.className = "popup-exclusao";

        const tituloElemento = document.createElement("h2");
        tituloElemento.textContent = titulo;

        const mensagemElemento = document.createElement("p");
        mensagemElemento.textContent = mensagem;

        conteudo.append(tituloElemento, mensagemElemento);

        const botoes = document.createElement("div");
        botoes.className = "popup-exclusao-botoes";

        const cancelar = document.createElement("button");
        cancelar.type = "button";
        cancelar.className = "popup-exclusao-cancelar";
        cancelar.textContent = "Cancelar";

        cancelar.addEventListener("click", () => overlay.remove());

        botoes.appendChild(cancelar);

        if (formulario) {
            const confirmar = document.createElement("button");
            confirmar.type = "submit";
            confirmar.className = "popup-exclusao-confirmar";
            confirmar.textContent = "Excluir";

            confirmar.addEventListener("click", () => {
                formulario.requestSubmit();
            });

            botoes.appendChild(confirmar);
        }

        conteudo.appendChild(botoes);
        overlay.appendChild(conteudo);
        document.body.appendChild(overlay);

        overlay.addEventListener("click", (evento) => {
            if (evento.target === overlay) {
                overlay.remove();
            }
        });
    }

    document.addEventListener("click", (evento) => {
        const botao = evento.target.closest(".botao-excluir");

        if (!botao) return;

        evento.preventDefault();

        const endereco = botao.href;
        const identificador = new URL(endereco).searchParams.get("id");

        if (!identificador) return;

        const formulario = document.createElement("form");
        formulario.method = "POST";
        formulario.action = endereco;

        const campoId = document.createElement("input");
        campoId.type = "hidden";
        campoId.name = "id";
        campoId.value = identificador;

        formulario.appendChild(campoId);

        criarPopup(
            "Confirmar exclusão",
            "Tem certeza de que deseja excluir este registro? Essa ação não poderá ser desfeita.",
            formulario
        );

        formulario.style.display = "none";
        document.body.appendChild(formulario);

        const confirmar = document.querySelector(".popup-exclusao-confirmar");

        confirmar.addEventListener("click", () => {
            formulario.requestSubmit();
        }, { once: true });
    });

    // Mostra erros retornados pelo PHP
    const parametros = new URLSearchParams(window.location.search);
    const erro = parametros.get("erro_exclusao");

    if (erro) {
        criarPopup("Não foi possível excluir", erro);

        parametros.delete("erro_exclusao");

        const novaUrl = window.location.pathname +
            (parametros.toString() ? "?" + parametros.toString() : "");

        window.history.replaceState({}, "", novaUrl);
    }
});
