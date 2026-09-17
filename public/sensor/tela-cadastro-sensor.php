<?php

include '../../infra/conexao.php';

$mensagem = '';
$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_sensor = $_POST['nome_sensor'];
    $tipo_sensor = $_POST['tipo_sensor'];
    $localizacao = $_POST['localizacao'];
    $unidade_medida = $_POST['unidade_medida'];
    $limite_alerta = $_POST['limite_alerta'];
    $status_inicial = $_POST['status_inicial'];
    $descricao = $_POST['descricao'];

    $sql = "INSERT INTO sensor (nome_sensor, tipo_sensor, localizacao, unidade_medida, limite_alerta, status_inicial, descricao) VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);

    if ($stmt) {

    $limite_alerta = (float) $limite_alerta;

    $stmt->bind_param(
        "ssssdss",
        $nome_sensor,
        $tipo_sensor,
        $localizacao,
        $unidade_medida,
        $limite_alerta,
        $status_inicial,
        $descricao
    );

   if ($stmt->execute()) {
    $mensagem = 'Novo sensor cadastrado com sucesso!';

   } else {
    $erro = 'Não foi possível cadastrar o sensor';
   }

   $stmt->close();
    } else {
        $erro = 'Erro ao preparar o cadastro do sensor.';
    }
}

?>


<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Sensor</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body>
    <header class="barra-superior">
        <h1>PIA Enterprise</h1>

    </header>

    <aside class="menu-lateral">
        <nav>
            <a href="../../index.php">Dashboard</a>
            <a class="ativo" href="tela-home-sensor.php">Sensores</a>
            <a href="../../public/trem/cadastro_trem.php">Trens</a>
            <a href="#">Rotas</a>
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

            <button class="botao-voltar-sensores" onclick="window.location.href='tela-home-sensor.php'">
                🠐 Voltar para sensores
            </button>
        </section>


        <section class="painel-cadastro-sensor">
            <form class="formulario-cadastro-sensor" method="POST"  >

             <!-- linha 1 -->
                <div class="linha-campos-formulario">
                    <div class="grupo-nome_sensor-sensor">
                        <label class="nome_sensor-sensor">
                            Nome do Sensor
                        </label>
                    </div>

                    <input type="text" name="nome_sensor" class="input-nome_sensorsensor">
                </div>


                <div class="grupo-tipo-sensor">

                    <label class="tipo-sensor">
                        Tipo do sensor
                    </label>

                    <input type="text" name="tipo_sensor" class="select-tipo-sensor">

                   
                </div>

                


                <!-- linha 2 -->
                 <div class="linha-campos-formulario">

                <div class="grupo-localizacao-sensor">

                    <label class="localizacao-sensor">
                        Localização
                    </label>

                    <input
                        type="text"
                        class="input-localizacao-sensor"
                        name="localizacao">

                                    </div>

                <div class="grupo-unidade-medida">

                    <label class="unidade-medida">
                        Unidade de medida
                    </label>

                   <input type="text" name="unidade_medida" class="select-unidade-medida">

                   

                </div>

                <div class="grupo-limite-alerta">

                    <label class="limite-alerta">
                        Limite de alerta
                    </label>

                    <input
                        type="text"
                        name="limite_alerta"
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
                            <input type="radio" name="status_inicial" value="ativo">
                            Ativo
                        </label>

                        <label class="opcao-status-inativo">
                            <input type="radio" name="status_inicial" value="inativo">
                            Inativo
                        </label>

                    </div>

                </div>

                <div class="grupo-descricao-sensor">

                    <label class="descricao-sensor">
                        Descrição
                    </label>

                    <textarea
                        name="descricao"
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