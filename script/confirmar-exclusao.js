
// ========================================
// ESTILO DO POPUP
// ========================================

const estiloPopup = document.createElement("style");

estiloPopup.textContent = `
    .popup-exclusao-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(5, 3, 22, 0.72);
        backdrop-filter: blur(4px);
    }

    .popup-exclusao {
        width: 100%;
        max-width: 420px;
        padding: 30px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 18px 50px rgba(0, 0, 0, 0.3);
        text-align: center;
        animation: aparecer-popup 0.2s ease;
    }

    .popup-exclusao h2 {
        margin: 0 0 12px;
        color: #111827;
        font-size: 21px;
        font-weight: 700;
    }

    .popup-exclusao p {
        margin: 0 0 24px;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }

    .popup-exclusao-botoes {
        display: flex;
        justify-content: center;
        gap: 12px;
    }

    .popup-exclusao-botoes button {
        min-width: 115px;
        height: 40px;
        padding: 0 16px;
        border: none;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
    }

    .popup-exclusao-cancelar {
        color: #1f2937;
        background: #e5e7eb;
    }

    .popup-exclusao-cancelar:hover {
        background: #d1d5db;
    }

    .popup-exclusao-confirmar {
        color: #ffffff;
        background: #dc2626;
    }

    .popup-exclusao-confirmar:hover {
        background: #b91c1c;
    }

    @keyframes aparecer-popup {
        from {
            opacity: 0;
            transform: translateY(10px) scale(0.97);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @media (max-width: 480px) {
        .popup-exclusao {
            padding: 24px 18px;
        }

        .popup-exclusao-botoes {
            flex-direction: column;
        }

        .popup-exclusao-botoes button {
            width: 100%;
        }
    }
`;

document.head.appendChild(estiloPopup);


// ========================================
// FUNCIONAMENTO DOS POPUPS
// ========================================

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

        // BOTÃO CANCELAR
        const cancelar = document.createElement("button");
        cancelar.type = "button";
        cancelar.className = "popup-exclusao-cancelar";
        cancelar.textContent = "Cancelar";

        cancelar.addEventListener("click", () => {
            overlay.remove();

            if (formulario) {
                formulario.remove();
            }
        });

        botoes.appendChild(cancelar);

        // BOTÃO CONFIRMAR
        if (formulario) {

            const confirmar = document.createElement("button");
            confirmar.type = "button";
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

        // FECHAR AO CLICAR FORA
        overlay.addEventListener("click", (evento) => {
            if (evento.target === overlay) {
                overlay.remove();

                if (formulario) {
                    formulario.remove();
                }
            }
        });

    }


    // ========================================
    // CONFIRMAÇÃO DE EXCLUSÃO
    // ========================================

    document.addEventListener("click", (evento) => {

        const botao = evento.target.closest(".botao-excluir");

        if (!botao) return;

        evento.preventDefault();

        const endereco = botao.href;
        const identificador = new URL(endereco).searchParams.get("id");

        if (!identificador) return;

        // CRIA FORMULÁRIO
        const formulario = document.createElement("form");

        formulario.method = "POST";
        formulario.action = endereco;
        formulario.style.display = "none";

        // ENVIA O ID DO REGISTRO
        const campoId = document.createElement("input");

        campoId.type = "hidden";
        campoId.name = "id";
        campoId.value = identificador;

        formulario.appendChild(campoId);
        document.body.appendChild(formulario);

        criarPopup(
            "Confirmar exclusão",
            "Tem certeza de que deseja excluir este registro? Essa ação não poderá ser desfeita.",
            formulario
        );

    });


    // ========================================
    // POPUP DE ERRO NA EXCLUSÃO
    // ========================================

    const parametros = new URLSearchParams(window.location.search);

    const erro = parametros.get("erro_exclusao");

    if (erro) {

        criarPopup(
            "Não foi possível excluir",
            erro
        );

        // REMOVE O ERRO DA URL
        parametros.delete("erro_exclusao");

        const novaUrl = window.location.pathname +
            (parametros.toString() ? "?" + parametros.toString() : "");

        window.history.replaceState({}, "", novaUrl);

    }

});
