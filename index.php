
<?php

// inicia a sessao pra conseguir acessar os dados do usuario durante a navegacao
session_start();

// isset verifica se a variavel existe e nao possui valor null
// ! inverte a condicao, verificando se o usuario nao esta conectado
if (!isset($_SESSION['usuario'])) {

    // header envia o usuario para a pagina inicial
    // Location indica qual pagina sera aberta
    header("Location: public/landing.php");

    // exit encerra a execucao do codigo depois do redirecionamento
    exit;
}


// ?? verifica se o valor existe e nao e null
// caso contrario, utiliza uma string vazia como valor padrao
$tipoUsuario = $_SESSION['tipo'] ?? '';


// === compara o valor e o tipo da variavel
// ? : funciona como um if e else em uma unica expressao
// se o tipo for administrador, mostra Administrador
// caso contrario, mostra Funcionario
$identificacao = $tipoUsuario === 'administrador'
    ? 'Administrador'
    : 'Funcionário';


// require_once inclui o arquivo de conexao com o banco de dados apenas uma vez
// esse arquivo disponibiliza a variavel $conexao
require_once 'infra/conexao.php';


// mysqli_query executa um comando SQL utilizando a conexao com o banco
// SELECT COUNT(*) conta quantos registros atendem a condicao
// AS total define um nome para a coluna que recebe a quantidade
// LOWER transforma o texto em letras minusculas
// TRIM remove espacos extras antes e depois do texto
// WHERE define a condicao utilizada para selecionar os registros
$consultaSensores = mysqli_query(
    $conexao,
    "SELECT COUNT(*) AS total FROM sensor
     WHERE LOWER(TRIM(status_inicial)) = 'ativo'"
);


// define zero como quantidade inicial de sensores ativos
$sensoresAtivos = 0;

// verifica se a consulta foi executada corretamente
if ($consultaSensores) {

    // mysqli_fetch_assoc transforma o resultado em um array associativo
    // cada coluna do banco passa a ser acessada pelo nome
    $resultadoSensores = mysqli_fetch_assoc($consultaSensores);

    // ['total'] acessa a quantidade de sensores encontrada
    // ?? utiliza zero caso o resultado seja null
    $sensoresAtivos = $resultadoSensores['total'] ?? 0;
}


// consulta a tabela trem para contar quantos trens estao em operacao
// COUNT(*) conta os registros que atendem a condicao
// WHERE status = 'operacao' seleciona somente os trens em operacao
$consultaTrens = mysqli_query(
    $conexao,
    "SELECT COUNT(*) AS total FROM trem
     WHERE status = 'operacao'"
);


// define zero como quantidade inicial de trens ativos
$trensAtivos = 0;

// verifica se a consulta foi executada corretamente
if ($consultaTrens) {

    // busca o resultado da consulta e transforma em um array associativo
    $resultadoTrens = mysqli_fetch_assoc($consultaTrens);

    // acessa a quantidade de trens encontrada
    // ?? utiliza zero caso nao exista um resultado
    $trensAtivos = $resultadoTrens['total'] ?? 0;
}


// consulta a tabela rotas para contar quantas rotas estao ativas
// LOWER transforma o status em letras minusculas
// TRIM remove espacos extras
// a condicao ativa considera os registros com status ativa
$consultaRotas = mysqli_query(
    $conexao,
    "SELECT COUNT(*) AS total FROM rotas
     WHERE LOWER(TRIM(status)) = 'ativa'"
);


// define zero como quantidade inicial de rotas ativas
$rotasAtivas = 0;

// verifica se a consulta foi executada corretamente
if ($consultaRotas) {

    // busca a quantidade de rotas retornada pelo banco
    $resultadoRotas = mysqli_fetch_assoc($consultaRotas);

    // acessa o valor da coluna total
    // ?? utiliza zero caso o resultado seja null
    $rotasAtivas = $resultadoRotas['total'] ?? 0;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - PIA Enterprise</title>
    <link rel="stylesheet" href="assets/style/style.css">
</head>

<body id="home-corpo">

    <header class="barra-superior">

        <h1>PIA Enterprise</h1>

        <aside class="menu-lateral">

            <nav>

                <a class="ativo" href="index.php">Dashboard</a>

                <a href="public/sensor/tela-home-sensor.php">Sensores</a>

                <a href="public/trem/lista_trem.php">Trens</a>

                <a href="public/rotas/tela-home-rotas.php">Rotas</a>

                <?php

                // verifica se o tipo do usuario conectado e administrador
                // somente o administrador pode visualizar o link de usuarios
                if ($tipoUsuario === 'administrador') {

                ?>

                    <a href="public/usuario/tela-home-usuario.php">Usuários</a>

                <?php

                // encerra a condicao iniciada pelo if
                }

                ?>

                <div class="caixa-usuario">

                    <span class="tipo-usuario">

                        <?php

                        // htmlspecialchars transforma caracteres especiais em entidades HTML
                        // isso ajuda a evitar que um texto seja interpretado como codigo HTML
                        // ENT_QUOTES converte aspas simples e duplas
                        // UTF-8 define a codificacao utilizada
                        echo htmlspecialchars($identificacao, ENT_QUOTES, 'UTF-8');

                        ?>

                    </span>

                    <a class="botao-logout" href="public/logout/logout.php">
                        Sair
                    </a>

                </div>

            </nav>

        </aside>

    </header>

    <main class="conteudo">

        <section class="painel-principal">

            <h2>Dashboard</h2>

            <div class="metricas">

                <article class="cartao-metrica">

                    <h3>Sensores ativos</h3>

                    <p class="valor-metrica">
                        <strong><?= $sensoresAtivos ?></strong>
                    </p>

                </article>

                <article class="cartao-metrica">

                    <h3>Trens ativos</h3>

                    <p class="valor-metrica">
                        <strong><?= $trensAtivos ?></strong>
                    </p>

                </article>

                <article class="cartao-metrica">

                    <h3>Rotas ativas</h3>

                    <p class="valor-metrica">
                        <strong><?= $rotasAtivas ?></strong>
                    </p>

                </article>

                <article class="cartao-metrica">

                    <h3>Clima em Joinville</h3>

                    <div class="linha-metrica">

                        <strong class="ciano" id="clima-temperatura">
                            -- °C
                        </strong>

                    </div>

                    <p id="clima-condicao">
                        Carregando clima...
                    </p>

                    <small id="clima-atualizado"></small>

                </article>

            </div>

            <section class="cartao-mapa">

                <iframe
                    src="https://www.google.com/maps?q=Joinville%2C+Santa+Catarina&output=embed"
                    width="100%"
                    height="100%"
                    style="border: 0;"
                    loading="lazy">
                </iframe>

            </section>

        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script/grafico.js"></script>
    <script src="script/clima.js"></script>

</body>

</html>
