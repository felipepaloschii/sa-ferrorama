(function () {
    var campoCep = document.getElementById("cep");
    var mensagem = document.getElementById("cep-mensagem");
    var logradouro = document.getElementById("logradouro");
    var bairro = document.getElementById("bairro");
    var cidade = document.getElementById("cidade");
    var uf = document.getElementById("uf");

    // se a pagina nao tiver os campos, nao faz nada
    if (!campoCep || !mensagem || !logradouro || !bairro || !cidade || !uf) {
        return;
    }

    function limpar() {
        logradouro.value = "";
        bairro.value = "";
        cidade.value = "";
        uf.value = "";
    }

    function buscar() {
        var cep = campoCep.value.replace(/\D/g, "").slice(0, 8);

        campoCep.value = cep.length > 5 ? cep.slice(0, 5) + "-" + cep.slice(5) : cep;

        if (cep.length < 8) {
            limpar();
            mensagem.textContent = "";
            return;
        }

        mensagem.textContent = "Consultando CEP...";

        fetch("https://viacep.com.br/ws/" + cep + "/json/")
            .then(function (r) { return r.json(); })
            .then(function (dados) {
                if (campoCep.value.replace(/\D/g, "") !== cep) {
                    return;
                }
                if (dados.erro) {
                    limpar();
                    mensagem.textContent = "CEP não encontrado.";
                    return;
                }
                logradouro.value = dados.logradouro || "";
                bairro.value = dados.bairro || "";
                cidade.value = dados.localidade || "";
                uf.value = dados.uf || "";
                mensagem.textContent = "Endereço preenchido.";
            })
            .catch(function () {
                limpar();
                mensagem.textContent = "Não foi possível consultar o CEP.";
            });
    }

    campoCep.addEventListener("input", buscar);

    // se a pagina abrir com CEP completo e sem endereco, busca sozinho
    if (campoCep.value.replace(/\D/g, "").length === 8 && logradouro.value === "") {
        buscar();
    }
})();