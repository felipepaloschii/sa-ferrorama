const temperaturaClima = document.getElementById("clima-temperatura");
const condicaoClima = document.getElementById("clima-condicao");
const atualizadoClima = document.getElementById("clima-atualizado");

const descricoesClima = {
    0: "Céu limpo",
    1: "Predominantemente limpo",
    2: "Parcialmente nublado",
    3: "Nublado",
    45: "Neblina",
    48: "Neblina com geada",
    51: "Garoa leve",
    53: "Garoa moderada",
    55: "Garoa intensa",
    61: "Chuva leve",
    63: "Chuva moderada",
    65: "Chuva intensa",
    80: "Pancadas de chuva leves",
    81: "Pancadas de chuva moderadas",
    82: "Pancadas de chuva fortes",
    95: "Trovoadas"
};

async function carregarClima() {
    try {
        const url =
            "https://api.open-meteo.com/v1/forecast" +
            "?latitude=-26.3044" +
            "&longitude=-48.8487" +
            "&current=temperature_2m,weather_code" +
            "&timezone=America%2FSao_Paulo";

        const resposta = await fetch(url);

        if (!resposta.ok) {
            throw new Error("Falha na consulta do clima");
        }

        const dados = await resposta.json();
        const climaAtual = dados.current;

        temperaturaClima.textContent =
            `${Math.round(climaAtual.temperature_2m)} °C`;

        condicaoClima.textContent =
            descricoesClima[climaAtual.weather_code] ||
            "Condição climática não identificada";

        atualizadoClima.textContent =
            "Atualizado às " +
            new Date().toLocaleTimeString("pt-BR", {
                hour: "2-digit",
                minute: "2-digit"
            });
    } catch (erro) {
        temperaturaClima.textContent = "-- °C";
        condicaoClima.textContent = "Clima indisponível no momento.";
        atualizadoClima.textContent = "Tente novamente mais tarde.";
    }
}

carregarClima();