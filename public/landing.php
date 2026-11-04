<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferrorama | Mobilidade ferroviária</title>
    <link rel="stylesheet" href="../assets/style/style.css">

    <style>
        .landing-conteudo {
            max-width: 1000px;
            margin: 0 auto;
            padding: 48px 24px;
        }

        .landing-destaque {
            padding: 48px 32px;
            border-radius: 16px;
            background: var(--painel, #202b3a);
            text-align: center;
        }

        .landing-destaque h2 {
            margin-top: 0;
            font-size: 2rem;
        }

        .landing-destaque p {
            max-width: 700px;
            margin: 16px auto;
            line-height: 1.6;
        }

        .landing-acoes {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 24px;
        }

        .landing-botao {
            display: inline-block;
            padding: 12px 22px;
            border-radius: 8px;
            background: var(--ambar, #e6a23c);
            color: #171717;
            text-decoration: none;
            font-weight: bold;
        }

        .landing-secoes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-top: 24px;
        }

        .landing-secao {
            padding: 22px;
            border-radius: 12px;
            background: var(--painel, #202b3a);
        }

        .landing-secao h3 {
            margin-top: 0;
        }
    </style>
</head>

<body>
    <header class="barra-superior">
        <h1>Ferrorama</h1>
    </header>

    <main class="landing-conteudo">
        <section class="landing-destaque">
            <h2>Gestão ferroviária em um só lugar</h2>
            <p>
                O Ferrorama é um projeto de gerenciamento ferroviário
                desenvolvido para organizar informações sobre trens, rotas
                e sensores, apoiando o acompanhamento das operações.
            </p>

            <div class="landing-acoes">
                <a class="landing-botao" href="usuario/tela-login.php">
                    Acessar o sistema
                </a>
                <a class="landing-botao" href="cep.php">
                    Consultar CEP
                </a>
            </div>
        </section>

        <section class="landing-secoes">
            <article class="landing-secao">
                <h3>Trens</h3>
                <p>Consulte e organize os registros dos trens cadastrados.</p>
            </article>

            <article class="landing-secao">
                <h3>Rotas</h3>
                <p>Visualize as rotas e informações relacionadas às viagens.</p>
            </article>

            <article class="landing-secao">
                <h3>Sensores</h3>
                <p>Acompanhe os registros de sensores disponíveis no sistema.</p>
            </article>
        </section>
    </main>
</body>
</html>