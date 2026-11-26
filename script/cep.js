
const campoCep = document.getElementById("cep");
const mensagemCep = document.getElementById("cep-mensagem");

const camposEndereco = {
    logradouro: document.getElementById("logradouro"),
    bairro: document.getElementById("bairro"),
    cidade: document.getElementById("cidade"),
    uf: document.getElementById("uf")
};

// verifica se os elementos existem na pagina
if (campoCep && mensagemCep) {

    // consulta automaticamente quando o cep tiver oito numeros
    campoCep.addEventListener("input", async function () {

        // remove caracteres que nao sejam numeros
        const cep = campoCep.value.replace(/\D/g, "");

        // aguarda o usuario terminar de digitar
        if (cep.length !== 8) {
            limparEndereco();
            mensagemCep.textContent = "";
            return;
        }

        // evita consultar novamente o mesmo cep
        if (campoCep.dataset.consultado === cep) {
            return;
        }

        // registra o cep que esta sendo consultado
        campoCep.dataset.consultado = cep;

        // informa que a consulta esta acontecendo
        mensagemCep.textContent = "Consultando CEP...";

        try {

            // consulta a API ViaCEP
            const resposta = await fetch("https://viacep.com.br/ws/" + cep + "/json/");

            // verifica se a requisicao funcionou
            if (!resposta.ok) {
                throw new Error("Falha na consulta");
            }

            // transforma a resposta em objeto
            const dados = await resposta.json();

            // verifica se o cep foi encontrado
            if (dados.erro) {
                limparEndereco();
                mensagemCep.textContent = "CEP não encontrado.";
                return;
            }

            // preenche os campos de endereco
            camposEndereco.logradouro.value = dados.logradouro || "";
            camposEndereco.bairro.value = dados.bairro || "";
            camposEndereco.cidade.value = dados.localidade || "";
            camposEndereco.uf.value = dados.uf || "";

            // informa que o endereco foi preenchido
            mensagemCep.textContent = "Endereço preenchido.";

        } catch (erro) {

            // permite tentar consultar novamente
            campoCep.dataset.consultado = "";

            mensagemCep.textContent =
                "Não foi possível consultar o CEP. Verifique sua conexão.";
        }
    });
}

// funcao para limpar os campos de endereco
function limparEndereco() {

    Object.values(camposEndereco).forEach(function (campo) {

        if (campo) {
            campo.value = "";
        }

    });
}
