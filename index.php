<?php

// inicia a sessao pra conseguir acessar os dados do usuario durante a navegacao
session_start();

// isset verifica se a variavel existe e nao possui valor null
// ! inverte a condicao, verificando se o usuario nao esta conectado
if (!isset($_SESSION['usuario'])) {

    // header envia um redirecionamento pra pagina inicial
    header("Location: public/landing.php");

    // exit encerra a execucao do codigo depois do redirecionamento
    exit;
}

// ?? verifica se o valor existe e nao e null
// caso contrario, utiliza uma string vazia como valor padrao
$tipoUsuario = $_SESSION['tipo'] ?? '';

// === compara o valor e o tipo da variavel
// ? : funciona como um if e else
$identificacao = $tipoUsuario === 'administrador'
    ? 'Administrador'
    : 'Funcionário';

// require_once inclui o arquivo de conexao apenas uma vez
// esse arquivo disponibiliza a variavel $conexao para acessar o banco
require_once 'infra/conexao.php';

// mysqli_query executa a consulta SQL utilizando a conexao com o banco
// COUNT(*) conta quantos sensores possuem status ativo
// AS total define o nome da coluna que recebe a contagem
// LOWER transforma o texto em minusculo e TRIM remove espacos extras
$consultaSensores = mysqli_query(
    $conexao,
    "SELECT COUNT(*) AS total FROM sensor
     WHERE LOWER(TRIM(status_inicial)) = 'ativo'"
);

// mysqli_fetch_assoc transforma o resultado em um array associativo
// ['total'] acessa o valor da coluna total
// ?? 0 utiliza zero como valor padrao caso o resultado seja null
$sensoresAtivos = mysqli_fetch_assoc($consultaSensores)['total'] ?? 0;

// consulta a tabela trem pra contar quantos trens estao em operacao
$consultaTrens = mysqli_query(
    $conexao,
    "SELECT COUNT(*) AS total FROM trem
     WHERE status = 'operacao'"
);

// pega a quantidade de trens retornada pelo banco
// ?? 0 define zero caso o resultado seja null
$trensAtivos = mysqli_fetch_assoc($consultaTrens)['total'] ?? 0;

// consulta a tabela rotas pra contar quantas rotas estao ativas
$consultaRotas = mysqli_query(
    $conexao,
    "SELECT COUNT(*) AS total FROM rotas
     WHERE status = 'ativo'"
);

// pega a quantidade de rotas retornada pelo banco
// ?? 0 define zero caso o resultado seja null
$rotasAtivas = mysqli_fetch_assoc($consultaRotas)['total'] ?? 0;

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

            

            <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
                <a href="public/usuario/tela-home-usuario.php">Usuários</a>
            <?php endif; ?>

            <div class="menu-conta">

            <button type="button" class="botao-conta" onclick="toggleConta()">
            <?= htmlspecialchars($identificacao) ?>
                <span class="seta-conta">▼</span>
            </button>

            <div class="dropdown-conta" id="dropdownConta">
            <a href="public/logout/logout.php">Sair da conta</a>
            </div>

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
                <p class="valor-metrica"><strong><?= $sensoresAtivos ?></strong></p>
            </article>

            <article class="cartao-metrica">
                <h3>Trens ativos</h3>
                <p class="valor-metrica"><strong><?= $trensAtivos ?></strong></p>
            </article>

            <article class="cartao-metrica">
                <h3>Rotas ativas</h3>
                <p class="valor-metrica"><strong><?= $rotasAtivas ?></strong></p>
            </article>

               <article class="cartao-metrica">
                <h3>Clima em Joinville</h3>

                <div class="linha-metrica">
                <strong class="ciano" id="clima-temperatura">-- °C</strong>
                </div>

                <p id="clima-condicao">Carregando clima...</p>
                <small id="clima-atualizado"></small>
                </article>


            </div>


              

                <section class="cartao-mapa">

                    <iframe
                        src="https://www.google.com/maps?q=S%C3%A3o%20Paulo&output=embed"
                        width="100%"
                        height="100%"
                        style="border: 0;"
                        loading="lazy">
                    </iframe>

                </section>

            </div>

        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script/grafico.js"></script>
    <script src="script/clima.js"></script>

</body>

</html>