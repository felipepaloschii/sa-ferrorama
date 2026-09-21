<?php 

include '../../infra/conexao.php';

$quantidade_ativos = 0;
$sensores = [];

$sql = "SELECT * FROM sensor ORDER BY id DESC";

$resultado = $conexao->query($sql);

if ($resultado) {

    while ($sensor = $resultado->fetch_assoc()) {

        $sensores[] = $sensor;

        if (strtolower(trim($sensor['status_inicial'])) == 'ativo') {
            $quantidade_ativos++;
        }
    }
}

?>




<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de Sensor</title>
    <link rel="stylesheet" href="../../assets/style/style.css">

</head>
<body>
   <body>
    <header class="barra-superior">
        <h1>PIA Enterprise</h1>

    </header>

    <aside class="menu-lateral">
        <nav>
            <a href="../../index.php">Dashboard</a>
            <a class="ativo" href="tela-home-sensor.php">Sensores</a>
            <a href="../trem/cadastro_trem.php">Trens</a>
            <a href="../rotas/tela-home-rotas.php">Rotas</a>
        </nav>
    </aside>

    <main class="area-principal">
        <section class="cabecalho-sensor">

         <div>

                <h1 class="titulo-sensores">
                    Sensores
                </h1>

                <p class="descricao-sensores">
                    Gerencie os sensores do sistema ferroviário
                </p>

            </div>
        </section>

       <section class="conteudo-lista-sensores">


        <div class="topo-lista-sensores">


            <div class="cartao-sensores-ativos">

                <div class="icone-sensor">

                    <span></span>
                    <span></span>
                    <span></span>

                </div>

                <div class="informacoes-sensor">

                    <h2>Sensores Ativos</h2>

                    <strong>
                        <?php echo $quantidade_ativos; ?>
                    </strong>

                </div>

            </div>


            <a href="tela-cadastro-sensor.php"
               class="botao-novo-sensor">

                + Novo Sensor

            </a>


        </div>

         <section class="painel-lista-sensores">


            <div class="titulo-painel-lista">

                <h2>Lista de Sensores</h2>

            </div>


            <div class="tabela-sensores">


                <div class="cabecalho-tabela">

                    <span>ID</span>

                    <span>Nome</span>

                    <span>Tipo</span>

                    <span>Localização</span>

                    <span>Unidade</span>

                    <span>Limite</span>

                    <span>Status</span>

                    <span>Ações</span>

                </div>


                <?php if (count($sensores) > 0): ?>


                    <?php foreach ($sensores as $sensor): ?>


                        <div class="linha-tabela">


                            <span>
                                <?php echo htmlspecialchars($sensor['id']); ?>
                            </span>


                            <span>
                                <?php echo htmlspecialchars($sensor['nome_sensor']); ?>
                            </span>


                            <span>
                                <?php echo htmlspecialchars($sensor['tipo_sensor']); ?>
                            </span>


                            <span>
                                <?php echo htmlspecialchars($sensor['localizacao']); ?>
                            </span>


                            <span>
                                <?php echo htmlspecialchars($sensor['unidade_medida']); ?>
                            </span>


                            <span>
                                <?php echo htmlspecialchars($sensor['limite_alerta']); ?>
                            </span>


                            <span>

                                <?php if (strtolower($sensor['status_inicial']) == 'ativo'): ?>

                                    <span class="status ativo">
                                        Ativo
                                    </span>

                                <?php else: ?>

                                    <span class="status inativo">
                                        Inativo
                                    </span>

                                <?php endif; ?>

                            </span>


                            <span class="acoes">


                                <a
                                    href="tela-edit-sensor.php?id=<?php echo $sensor['id']; ?>"
                                    class="botao-editar"
                                    title="Editar">

                                    ✎

                                </a>


                                <a
                                    href="tela-excluir-sensor.php?id=<?php echo $sensor['id']; ?>"
                                    class="botao-excluir"
                                    title="Excluir">

                                    🗑

                                </a>


                            </span>


                        </div>


                    <?php endforeach; ?>


                <?php else: ?>


                    <div class="nenhum-sensor">

                        Nenhum sensor cadastrado.

                    </div>


                <?php endif; ?>


            </div>


        </section>


    </section>


</main>

</body>
</html>