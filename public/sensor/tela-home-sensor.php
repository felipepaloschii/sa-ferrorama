
<?php

// verifica se a sessão ainda não foi iniciada
// session_status verifica o estado atual da sessão
// !== verifica se o estado é diferente de PHP_SESSION_ACTIVE
if (session_status() !== PHP_SESSION_ACTIVE) {

    // inicia a sessão pra acessar os dados do usuário
    session_start();
}

// isset verifica se a variável existe e não possui valor null
// ! inverte a verificação
// || significa OU, verificando se algum dos dados está faltando
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['tipo'])) {

    // redireciona pra tela de login caso o usuário não esteja conectado
    header('Location: ../usuario/tela-login.php');

    // encerra a execução depois do redirecionamento
    exit;
}

// ?? verifica se o valor existe e não é null
// caso contrário, utiliza uma string vazia como valor padrão
$tipoUsuario = $_SESSION['tipo'] ?? '';

// === compara o valor e o tipo da variável
// ? : funciona como um if e else
$identificacao = $tipoUsuario === 'administrador'
    ? 'Administrador'
    : 'Funcionário';

// inclui o arquivo de conexão com o banco de dados
require_once '../../infra/conexao.php';

// verifica se o tipo do usuário conectado é administrador
// === compara o valor e o tipo ao mesmo tempo
$administrador = $_SESSION['tipo'] === 'administrador';

// cria a consulta pra buscar os sensores e os dados das rotas relacionadas
$sql = "SELECT
            sensor.*,
            rotas.nome_rota,
            rotas.codigo AS codigo_rota
        FROM sensor
        LEFT JOIN rotas ON sensor.id_rota = rotas.id_rota
        ORDER BY sensor.id DESC";

// LEFT JOIN relaciona as tabelas e permite mostrar sensores sem rota
// ON define que o id_rota do sensor deve corresponder ao id_rota da rota
// AS cria um apelido para a coluna codigo da tabela rotas
// ORDER BY organiza os sensores pelo id em ordem decrescente

// query executa a consulta no banco de dados
$resultado = $conexao->query($sql);

// cria um array vazio pra armazenar os sensores encontrados
$sensores = [];

// inicia a quantidade de sensores ativos em zero
$quantidade_ativos = 0;

// verifica se a consulta foi executada corretamente
if ($resultado) {

    // while repete o comando enquanto existirem registros
    // fetch_assoc transforma cada registro em um array associativo
    while ($sensor = $resultado->fetch_assoc()) {

        // adiciona os dados do sensor ao array
        $sensores[] = $sensor;

        // strtolower transforma o texto em letras minúsculas
        // trim remove espaços do início e do final
        // === verifica se o status é exatamente igual a ativo
        if (strtolower(trim($sensor['status_inicial'])) === 'ativo') {

            // ++ aumenta a quantidade de sensores ativos em 1
            $quantidade_ativos++;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Listagem de Sensores</title>

    <link rel="stylesheet" href="../../assets/style/style.css">

</head>

<body>

<header class="barra-superior">

    <h1>PIA Enterprise</h1>

    <aside class="menu-lateral">

        <nav>

            <a href="../../index.php">Dashboard</a>

            <a class="ativo" href="tela-home-sensor.php">Sensores</a>

            <a href="../trem/lista_trem.php">Trens</a>

            <a href="../rotas/tela-home-rotas.php">Rotas</a>

            <?php if ($administrador): ?>
                <a href="../usuario/tela-home-usuario.php">Usuários</a>
            <?php endif; ?>

            <div class="menu-conta">

                <button type="button" class="botao-conta" onclick="toggleConta()">

                    <?= htmlspecialchars($identificacao, ENT_QUOTES, 'UTF-8'); ?>

                    <span class="seta-conta">▼</span>

                </button>

                <div class="dropdown-conta" id="dropdownConta">

                    <a href="../logout/logout.php">Sair da conta</a>

                </div>

            </div>

        </nav>

    </aside>

</header>

<main class="area-principal">

    <section class="cabecalho-sensor">

        <div>
            <h1 class="titulo-sensores">Listagem de Sensores</h1>
        </div>

        <div class="topo-lista-sensores">

            <?php if ($administrador): ?>
                <a
                    class="botao-novo-sensor botao-novo-trem"
                    href="tela-cadastro-sensor.php">
                    + Novo Sensor
                </a>
            <?php endif; ?>

        </div>

    </section>

    <section class="painel-lista-sensores">

        <div class="tabela-sensores-grade">

            <div class="cabecalho-sensores-grade">

                <span>ID</span>
                <span>Nome</span>
                <span>Tipo</span>
                <span>Localização</span>
                <span>Rota associada</span>
                <span>Unidade</span>
                <span>Limite</span>
                <span>Status</span>

                <?php if ($administrador): ?>
                    <span>Ações</span>
                <?php endif; ?>

            </div>

            <?php if (count($sensores) > 0): ?>

                <?php foreach ($sensores as $sensor): ?>

                    <div class="linha-sensores-grade">

                        <span>
                            <?= htmlspecialchars($sensor['id'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>

                        <span>
                            <?= htmlspecialchars($sensor['nome_sensor'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>

                        <span>
                            <?= htmlspecialchars($sensor['tipo_sensor'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>

                        <span>
                            <?= htmlspecialchars($sensor['localizacao'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>

                        <span>
                            <?php if (!empty($sensor['nome_rota'])): ?>

                                <?= htmlspecialchars($sensor['nome_rota'], ENT_QUOTES, 'UTF-8'); ?>

                                (<?= htmlspecialchars($sensor['codigo_rota'], ENT_QUOTES, 'UTF-8'); ?>)

                            <?php else: ?>

                                Sem rota associada

                            <?php endif; ?>
                        </span>

                        <span>
                            <?= htmlspecialchars($sensor['unidade_medida'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>

                        <span>
                            <?= htmlspecialchars($sensor['limite_alerta'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>

                        <span>

                            <?php if (strtolower(trim($sensor['status_inicial'])) === 'ativo'): ?>

                                <span class="status ativo">Ativo</span>

                            <?php else: ?>

                                <span class="status inativo">Inativo</span>

                            <?php endif; ?>

                        </span>

                        <?php if ($administrador): ?>

                            <span class="acoes-sensores-grade">

                                <a
                                    href="tela-edit-sensor.php?id=<?= (int) $sensor['id']; ?>"
                                    class="botao-editar"
                                    title="Editar">
                                    E
                                </a>

                                <a
                                    href="tela-excluir-sensor.php?id=<?= (int) $sensor['id']; ?>"
                                    class="botao-excluir"
                                    title="Excluir">
                                    D
                                </a>

                            </span>

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="nenhum-sensor">
                    Nenhum sensor cadastrado.
                </div>

            <?php endif; ?>

        </div>

    </section>

</main>

</body>
</html>
