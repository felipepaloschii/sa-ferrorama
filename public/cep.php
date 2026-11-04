<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de CEP | Ferrorama</title>
    <link rel="stylesheet" href="../assets/style/style.css">

    <style>
        .cep-conteudo {
            max-width: 700px;
            margin: 40px auto;
            padding: 24px;
        }

        .cep-formulario {
            display: grid;
            gap: 14px;
            padding: 24px;
            border-radius: 12px;
            background: var(--painel, #202b3a);
        }

        .cep-formulario label {
            display: block;
            margin-bottom: 6px;
        }

        .cep-formulario input {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
        }

        .cep-formulario button {
            padding: 12px;
            cursor: pointer;
        }

        #cep-mensagem {
            min-height: 1.4em;
        }
    </style>
</head>

<body>
    <header class="barra-superior">
        <h1>Consulta de CEP</h1>
    </header>

    <main class="cep-conteudo">
        <form class="cep-formulario" id="form-cep">
            <h2>Buscar endereço</h2>

            <div>
                <label for="cep">CEP</label>
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
            <p id="cep-mensagem" role="status"></p>

            <div>
                <label for="logradouro">Logradouro</label>
                <input id="logradouro" name="logradouro" autocomplete="street-address">
            </div>

            <div>
                <label for="bairro">Bairro</label>
                <input id="bairro" name="bairro" autocomplete="address-level3">
            </div>

            <div>
                <label for="cidade">Cidade</label>
                <input id="cidade" name="cidade" autocomplete="address-level2">
            </div>

            <div>
                <label for="uf">Estado</label>
                <input id="uf" name="uf" maxlength="2" autocomplete="address-level1">
            </div>

            <a href="landing.php">Voltar para a página inicial</a>
        </form>
    </main>

    <script src="../script/cep.js"></script>
</body>
</html>