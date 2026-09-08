<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FJL Tech</title>
    <link rel="stylesheet" href="../assets/style/style.css">
</head>

<body id="login-corpo">

    <main id="login-area">
        <section class="caixa-login">

            <form id="form-login">
                <h2 id="titulo">Login - Administrador</h2>

                <div class="conjunto">
                    <label for="email" class="label-form">Usuario:</label>
                    <input type="email" class="caixa-escrita" id="email" placeholder="Usuario" required>
                </div>

                <div class="conjunto">
                    <label for="senha" class="label-form">Senha:</label>
                    <input type="password" class="caixa-escrita" id="senha" placeholder="Senha" required>
                </div>

                <button id="botao-envio" type="submit">
                    Entrar
                </button>
            </form>

            <div id="mensagem"></div>

            <div class="linha"></div>

            <div class="toggle" id="toggle">
                <p>Não tem conta? Cadastre-se</p>
            </div>

        </section>
    </main>

    <script src="script/login.js"></script>
</body>

</html>