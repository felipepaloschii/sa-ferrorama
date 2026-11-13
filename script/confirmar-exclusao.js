
document.addEventListener('DOMContentLoaded', () => {
    const formularios = document.querySelectorAll('.botoes-excluir-sensor form');

    formularios.forEach(formulario => {
        formulario.addEventListener('submit', (evento) => {
            evento.preventDefault();

            const botao = formulario.querySelector(
                'button[type="submit"]'
            );

            const nomeAcao = botao
                ? botao.textContent.trim().toLowerCase()
                : 'registro';

            abrirPopup(nomeAcao, () => {
                formulario.submit();
            });
        });
    });
});

function abrirPopup(nomeAcao, confirmar) {
    const popup = document.createElement('div');

    popup.className = 'popup-exclusao';

    popup.innerHTML = `
        <div class="popup-exclusao-conteudo" role="dialog"
             aria-modal="true" aria-labelledby="titulo-popup">

            <div class="popup-exclusao-icone">!</div>

            <h2 id="titulo-popup">Confirmar exclusão</h2>

            <p>
                Tem certeza de que deseja excluir este ${nomeAcao}?
                Essa ação não poderá ser desfeita.
            </p>

            <div class="popup-exclusao-botoes">
                <button type="button" class="popup-cancelar">
                    Cancelar
                </button>

                <button type="button" class="popup-confirmar">
                    Excluir
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(popup);

    const fechar = () => popup.remove();

    popup.querySelector('.popup-cancelar')
        .addEventListener('click', fechar);

    popup.querySelector('.popup-confirmar')
        .addEventListener('click', () => {
            fechar();
            confirmar();
        });

    popup.addEventListener('click', (evento) => {
        if (evento.target === popup) {
            fechar();
        }
    });

    const teclaEscape = (evento) => {
        if (evento.key === 'Escape') {
            fechar();
            document.removeEventListener('keydown', teclaEscape);
        }
    };

    document.addEventListener('keydown', teclaEscape);

    popup.querySelector('.popup-cancelar').focus();
}
