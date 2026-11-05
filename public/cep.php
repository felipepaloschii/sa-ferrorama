<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de CEP | Ferrorama</title>

    <style>
        :root {
            --noite: #080617;
            --painel: #202c3b;
            --ambar: #eda52f;
            --fundo: #f4f6fa;
            --texto: #f8fafc;
            --suave: #cbd2dc;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--fundo);
            color: var(--noite);
            font-family: Arial, Helvetica, sans-serif;
        }

        header {
            min-height: 72px;
            display: flex;
            align-items: center;
            padding: 0 5%;
            background: var(--noite);
            box-shadow: 0 3px 12px rgba(8, 6, 23, .14);
        }

        header a {
            color: #fff;
            font-size: 24px;
            font-weight: 800;
            text-decoration: none;
        }

        main {
            width: min(1000px, 92%);
            margin: 52px auto;
        }

        .cartao-cep {
            padding: 38px 48px;
            border-radius: 16px;
            background: var(--painel);
            box-shadow: 0 12px 28px rgba(8, 6, 23, .12);
        }

        .cartao-cep h1 {
            margin: 0 0 10px;
            color: var(--texto);
            font-size: 30px;
        }

        .introducao {
            margin: 0 0 28px;
            color: var(--suave);
            line-height: 1.6;
        }

        .campo {
            min-width: 0;
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--texto);
            font-size: 14px;
            font-weight: 700;
        }

        input {
            width: 100%;
            min-width: 0;
            height: 60px;
            padding: 12px 16px;
            border: 1px solid #d7dce4;
            border-radius: 8px;
            background: #fff;
            color: #172033;
            font: inherit;
            font-size: 17px;
        }

        input:focus {
            outline: 3px solid rgba(237, 165, 47, .3);
            border-color: var(--ambar);
        }

        .linha-cep {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 14px;
            align-items: end;
        }

        .linha-cep .campo {
            margin: 0;
        }

        button {
            height: 60px;
            padding: 0 28px;
            border: 0;
            border-radius: 8px;
            background: var(--ambar);
            color: var(--noite);
            font-size: 15px;
            font-weight: 700;
            white-space: nowrap;
            cursor: pointer;
            transition: background .2s, transform .2s;
        }

        button:hover {
            background: #f7b84f;
            transform: translateY(-1px);
        }

        .mensagem {
            min-height: 20px;
            margin: 16px 0 0;
            color: var(--suave);
            font-size: 14px;
        }

        .resultado {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 22px;
            margin-top: 28px;
            padding-top: 28px;
            border-top: 1px solid #465363;
        }

        .resultado .campo {
            margin: 0;
        }

        .resultado input {
            background: #e9edf2;
        }

        @media (max-width: 600px) {
            main {
                margin: 28px auto;
            }

            .cartao-cep {
                padding: 26px 20px;
            }

            .linha-cep {
                grid-template-columns: 1fr;
            }

            .linha-cep button {
                width: 100%;
            }

            .resultado {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header>
        <a href="../landing.php">Ferrorama</a>
    </header>

    <main>
        <section class="cartao-cep">
            <h1>Consulta de CEP</h1>
            <p class="introducao">
                Digite um CEP para encontrar o endereço correspondente.
            </p>

            <form id="formCep">
                <label for="cep">CEP</label>

                <div class="linha-cep">
                    <div class="campo">
                        <input
                            id="cep"
                            name="cep"
                            type="text"
                            inputmode="numeric"
                            maxlength="9"
                            placeholder="00000-000"
                            autocomplete="postal-code"
                            required
                        >
                    </div>

                    <button type="submit">Consultar CEP</button>
                </div>

                <p class="mensagem" id="mensagem" role="status"></p>
            </form>

            <div class="resultado">
                <div class="campo">
                    <label for="logradouro">Rua</label>
                    <input id="logradouro" type="text" readonly>
                </div>

                <div class="campo">
                    <label for="bairro">Bairro</label>
                    <input id="bairro" type="text" readonly>
                </div>

                <div class="campo">
                    <label for="cidade">Cidade</label>
                    <input id="cidade" type="text" readonly>
                </div>

                <div class="campo">
                    <label for="estado">Estado</label>
                    <input id="estado" type="text" readonly>
                </div>
            </div>
        </section>
    </main>

    <script>
        const formulario = document.getElementById("formCep");
        const campoCep = document.getElementById("cep");
        const mensagem = document.getElementById("mensagem");

        campoCep.addEventListener("input", () => {
            let valor = campoCep.value.replace(/\D/g, "").slice(0, 8);

            if (valor.length > 5) {
                valor = valor.replace(/^(\d{5})(\d)/, "$1-$2");
            }

            campoCep.value = valor;
        });

        formulario.addEventListener("submit", async (evento) => {
            evento.preventDefault();

            const cep = campoCep.value.replace(/\D/g, "");
            mensagem.textContent = "";

            if (cep.length !== 8) {
                mensagem.textContent = "Digite um CEP válido com 8 números.";
                return;
            }

            try {
                mensagem.textContent = "Consultando endereço...";

                const resposta = await fetch(
                    `https://viacep.com.br/ws/${cep}/json/`
                );

                if (!resposta.ok) {
                    throw new Error("Não foi possível consultar o CEP.");
                }

                const dados = await resposta.json();

                if (dados.erro) {
                    throw new Error("CEP não encontrado.");
                }

                document.getElementById("logradouro").value =
                    dados.logradouro || "";

                document.getElementById("bairro").value =
                    dados.bairro || "";

                document.getElementById("cidade").value =
                    dados.localidade || "";

                document.getElementById("estado").value =
                    dados.uf || "";

                mensagem.textContent = "Endereço encontrado.";
            } catch (erro) {
                document.getElementById("logradouro").value = "";
                document.getElementById("bairro").value = "";
                document.getElementById("cidade").value = "";
                document.getElementById("estado").value = "";

                mensagem.textContent =
                    erro.message || "Não foi possível consultar o CEP.";
            }
        });
    </script>

    <center>
        <button class="botao-voltar" onclick="window.location.href='landing.php'">
            ← Voltar para a página inicial
        </button>
    </center>
</body>
</html>