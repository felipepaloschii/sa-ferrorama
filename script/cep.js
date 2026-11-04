const formularioCep = document.getElementById("form-cep");
const campoCep = document.getElementById("cep");
const mensagemCep = document.getElementById("cep-mensagem");

const camposEndereco = {
    logradouro: document.getElementById("logradouro"),
    bairro: document.getElementById("bairro"),
    cidade: document.getElementById("cidade"),
    uf: document.getElementById("uf")
};

formularioCep.addEventListener("submit", async (evento) => {
    evento.preventDefault();

    const cep = campoCep.value.replace(/\D/g, "");

    if (!/^\d{8}$/.test(cep)) {
        mensagemCep.textContent = "Digite um CEP com 8 números.";
        return;
    }

    mensagemCep.textContent = "Consultando CEP...";

    try {
        const resposta = await fetch(`https://viacep.com.br/ws/${cep}/json/`);

        if (!resposta.ok) {
            throw new Error("Falha na consulta");
        }

        const dados = await resposta.json();

        if (dados.erro) {
            mensagemCep.textContent = "CEP não encontrado.";
            limparEndereco();
            return;
        }

        camposEndereco.logradouro.value = dados.logradouro || "";
        camposEndereco.bairro.value = dados.bairro || "";
        camposEndereco.cidade.value = dados.localidade || "";
        camposEndereco.uf.value = dados.uf || "";

        mensagemCep.textContent = "Endereço preenchido.";
    } catch (erro) {
        mensagemCep.textContent =
            "Não foi possível consultar o CEP. Verifique a conexão e tente novamente.";
    }
});

function limparEndereco() {
    Object.values(camposEndereco).forEach((campo) => {
        campo.value = "";
    });
}