<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - FJL Tech</title>
    <link rel="stylesheet" href="../assets/style/style.css">
</head>

<body id="login-corpo">

    <main id="login-area">
        <section class="caixa-login">

            <form id="form-login">
                <h2 id="titulo">Cadastrar Usuário</h2>

                <div class="conjunto">
                    <label for="email" class="label-form">Usuario:</label>
                    <input type="email" class="caixa-escrita" id="email" placeholder="Usuario" required>
                </div>

                <div class="conjunto">
                    <label for="senha" class="label-form">Senha:</label>
                    <input type="password" class="caixa-escrita" id="senha" placeholder="Senha" required>
                </div>

                <button id="botao-envio" type="submit">
                    Cadastrar
                </button>
            </form>

            <div id="mensagem"></div>

            <div class="linha"></div>

          

        </section>
    </main>

    <script src="script/login.js"></script>
</body>

</html>