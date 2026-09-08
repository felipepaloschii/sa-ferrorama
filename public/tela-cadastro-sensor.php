<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Sensor</title>
    <link rel="stylesheet" href="../assets/style/style.css">
</head>

<body>
    <header class="barra-superior">
        <h1>FJL Tech</h1>

        <div class="acoes-superiores">
            <span class="ponto-online"></span>
            <span class="texto-online">Online</span>
            <span class="icone-sino"></span>
            <span class="icone-usuario"></span>
        </div>
    </header>

    <aside class="menu-lateral">
        <nav>
            <a href="#">Dashboard</a>
            <a class="ativo" href="#">Sensores</a>
            <a href="#">Alertas</a>
            <a href="#">Relat&oacute;rios</a>
            <a href="#">Configura&ccedil;&otilde;es</a>
        </nav>
    </aside>

    <main class="area-principal">
        <section class="cabecalho-cadastro-sensor">

            <div class="informacoes-pagina">

                <h1 class="titulo-pagina">
                    Cadastrar novos sensores
                </h1>

                <p class="descricao-pagina">
                    Preencha as informações para cadastrar um novo sensor.
                </p>
            </div>

            <button class="botao-voltar-sensores">
                🠐 Voltar para sensores
            </button>
        </section>


        <section class="painel-cadastro-sensor">
            <form class="formulario-cadastro-sensor">

             <!-- linha 1 -->
                <div class="linha-campos-formulario">
                    <div class="grupo-nome-sensor">
                        <label class="nome-sensor">
                            Nome do Sensor
                        </label>
                    </div>

                    <input type="text" class="input-nomesensor">
                </div>


                <div class="grupo-tipo-sensor">

                    <label class="tipo-sensor">
                        Tipo do sensor
                    </label>

                    <select class="select-tipo-sensor">

                    </select>
                </div>

                <div class="grupo-id-sensor">

                    <label class="id-sensor">
                        ID do sensor
                    </label>

                    <input type="text" class="input-id-sensor">

                </div>


                <!-- Este é um comentário válido em HTML linha 2 -->
                 <div class="linha-campos-formulario">

                <div class="grupo-localizacao-sensor">

                    <label class="localizacao-sensor">
                        Localização
                    </label>

                    <input
                        type="text"
                        class="input-localizacao-sensor">


                                    </div>

                <div class="grupo-unidade-medida">

                    <label class="unidade-medida">
                        Unidade de medida
                    </label>

                    <select class="select-unidade-medida">

                    </select>

                </div>

                <div class="grupo-limite-alerta">

                    <label class="limite-alerta">
                        Limite de alerta
                    </label>

                    <input
                        type="text"
                        class="input-limite-alerta">

                </div>

            </div>

            <div class="linha-status-e-descricao">

                <div class="grupo-status-sensor">

                    <label class="status-sensor">
                        Status Inicial
                    </label>

                    <div class="opcoes-status-sensor">

                        <label class="opcao-status-ativo">
                            <input type="radio">
                            Ativo
                        </label>

                        <label class="opcao-status-inativo">
                            <input type="radio">
                            Inativo
                        </label>

                    </div>

                </div>

                <div class="grupo-descricao-sensor">

                    <label class="descricao-sensor">
                        Descrição
                    </label>

                    <textarea
                        class="textarea-descricao-sensor">
                    </textarea>

                </div>

            </div>

    

            <div class="area-botoes-formulario">

                <button
                    class="botao-salvar-sensor">

                    Salvar sensor

                </button>

                <button
                    class="botao-cancelar-cadastro">

                    Cancelar

                </button>



                </div>
            </form>
        </section>
    </main>
</body>

</html>