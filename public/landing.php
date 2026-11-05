<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferrorama | Gestão ferroviária</title>
    <link rel="stylesheet" href="../assets/style/style.css">

    <style>
        :root {
            --noite: #080617;
            --painel: #202c3b;
            --ambar: #cbc9f7;
            --fundo: #f4f6fa;
            --texto: #080617;
            --texto-claro: #f8fafc;
            --texto-suave: #cbd2dc;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--fundo);
            color: var(--texto);
            font-family: Arial, Helvetica, sans-serif;
        }

        .cabecalho-landing {
            height: 72px;
            display: flex;
            align-items: center;
            padding: 0 5%;
            background: var(--noite);
            box-shadow: 0 3px 12px rgba(8, 6, 23, .14);
        }

        .marca-landing {
            color: white;
            font-size: 25px;
            font-weight: 800;
            text-decoration: none;
        }

        .conteudo-landing {
            width: min(1160px, 90%);
            margin: 58px auto 70px;
        }

        .hero-landing {
            padding: 62px 7%;
            border-radius: 18px;
            background: var(--painel);
            text-align: center;
            box-shadow: 0 12px 28px rgba(8, 6, 23, .10);
        }

        .hero-landing h1 {
            margin: 0 0 18px;
            color: var(--texto-claro);
            font-size: clamp(30px, 4vw, 43px);
            line-height: 1.2;
        }

        .hero-landing h1 span {
            color: var(--ambar);
        }

        .hero-landing p {
            max-width: 780px;
            margin: 0 auto;
            color: var(--texto-suave);
            font-size: 17px;
            line-height: 1.7;
        }

        .botoes-landing {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 28px;
        }

        .botao-landing {
            min-height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 25px;
            border: 2px solid var(--ambar);
            border-radius: 9px;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            transition: background .2s, transform .2s;
        }

        .botao-landing:hover {
            transform: translateY(-2px);
        }

        .botao-principal {
            background: var(--ambar);
            color: var(--noite);
        }

        .botao-principal:hover {
            background: #f7b84f;
        }

        .botao-secundario {
            background: transparent;
            color: var(--ambar);
        }

        .botao-secundario:hover {
            background: rgba(237, 165, 47, .10);
        }

        .recursos-landing {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            margin-top: 28px;
        }

        .cartao-landing {
            min-height: 165px;
            padding: 28px 26px;
            border-radius: 15px;
            background: var(--painel);
            box-shadow: 0 8px 20px rgba(8, 6, 23, .08);
            transition: transform .2s, box-shadow .2s;
        }

        .cartao-landing:hover {
            transform: translateY(-3px);
            box-shadow: 0 13px 25px rgba(8, 6, 23, .14);
        }

        .cartao-landing h2 {
            margin: 0 0 10px;
            color: var(--texto-claro);
            font-size: 22px;
        }

        .cartao-landing h2::after {
            content: "";
            display: block;
            width: 42px;
            height: 3px;
            margin-top: 10px;
            border-radius: 4px;
            background: var(--ambar);
        }

        .cartao-landing p {
            margin: 0;
            color: var(--texto-suave);
            font-size: 16px;
            line-height: 1.6;
        }

        @media (max-width: 800px) {
            .recursos-landing {
                grid-template-columns: 1fr;
            }

            .conteudo-landing {
                margin-top: 32px;
            }
        }

        @media (max-width: 520px) {
            .conteudo-landing {
                width: 92%;
            }

            .hero-landing {
                padding: 40px 22px;
            }

            .hero-landing p {
                font-size: 16px;
            }

            .botoes-landing {
                flex-direction: column;
            }

            .botao-landing {
                width: 100%;
            }

            .cartao-landing {
                min-height: auto;
                padding: 24px 22px;
            }
        }
    </style>
</head>

<body>
    <header class="cabecalho-landing">
        <a class="marca-landing" href="landing.php">PIA Enterprise</a>
    </header>

    <main class="conteudo-landing">
        <section class="hero-landing">
            <h1>Gestão ferroviária <span>em um só lugar</span></h1>

            <p>
                O Ferrorama reúne as informações de trens, rotas e sensores
                em um só sistema, facilitando o acompanhamento e a organização
                das operações ferroviárias.
            </p>

            <div class="botoes-landing">
                <a class="botao-landing botao-principal" href="usuario/tela-login.php">
                    Acessar o sistema
                </a>

                <a class="botao-landing botao-secundario"
                   href="cep.php"
                   target="_blank"
                   rel="noopener">
                    Consultar CEP
                </a>
            </div>
        </section>

        <section class="recursos-landing" aria-label="Recursos do sistema">
            <article class="cartao-landing">
                <h2>Trens</h2>
                <p>
                    Consulte os trens cadastrados e mantenha as informações
                    organizadas em um só lugar.
                </p>
            </article>

            <article class="cartao-landing">
                <h2>Rotas</h2>
                <p>
                    Visualize as rotas e consulte os dados relacionados
                    às viagens.
                </p>
            </article>

            <article class="cartao-landing">
                <h2>Sensores</h2>
                <p>
                    Acompanhe os registros dos sensores e tenha as informações
                    disponíveis para consulta.
                </p>
            </article>
        </section>
    </main>
</body>
</html>