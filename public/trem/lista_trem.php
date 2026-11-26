
<?php

// session_status() verifica se já existe uma sessão ativa no PHP
// Se não existir, session_start() inicia uma nova sessão
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verifica se o usuário possui um ID e um tipo de conta salvos na sessão
if (
    !isset($_SESSION['id_usuario']) ||
    !isset($_SESSION['tipo'])
) {
    header('Location: ../usuario/tela-login.php');
    exit;
}

// Verifica se o tipo de conta é válido
if (!in_array($_SESSION['tipo'], ['administrador', 'funcionario'], true)) {
    session_destroy();
    header('Location: ../usuario/tela-login.php');
    exit;
}

// Verifica se o usuário é administrador
$administrador = $_SESSION['tipo'] === 'administrador';

// Guarda o tipo de usuário para utilizar no cabeçalho
$tipoUsuario = $_SESSION['tipo'] ?? '';

// Inclui o arquivo de conexão com o banco de dados
require_once '../../infra/conexao.php';

// Guarda o nome da tabela que será utilizada
$tabela = 'trem';

// Cria um array com os nomes das colunas da tabela trem
$col = [
    'id'         => 'id',
    'nome'       => 'nome_trem',
    'modelo'     => 'modelo_trem',
    'local'      => 'localizacao_i',
    'ano'        => 'ano',
    'capacidade' => 'capacidade',
    'status'     => 'status',
    'codigo'     => 'codigo',
];

// Cria os textos que serão exibidos para cada status
$rotulosStatus = [
    'operacao'   => 'Em operação',
    'parado'     => 'Parado',
    'manutencao' => 'Manutenção',
    'inativo'    => 'Inativo',
];

// Busca os trens e o email do usuário responsável
// O LEFT JOIN permite mostrar trens mesmo sem um responsável associado
$sql = "SELECT trem.*, usuario.email AS usuario_responsavel
        FROM `$tabela` AS trem
        LEFT JOIN usuario
            ON trem.id_usuario = usuario.id_usuario
        ORDER BY trem.id";

// Executa a consulta no banco de dados
$resultado = mysqli_query($conexao, $sql);

// Verifica se ocorreu algum erro na consulta
if (!$resultado) {
    die('Erro ao consultar os trens: ' . mysqli_error($conexao));
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Listagem de Trens</title>

    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body>

    <header class="barra-superior">

        <h1>PIA Enterprise</h1>

        <aside class="menu-lateral">

            <nav>

                <a href="../../index.php">Dashboard</a>

                <a href="../sensor/tela-home-sensor.php">Sensores</a>

                <a class="ativo" href="lista_trem.php">Trens</a>

                <a href="../rotas/tela-home-rotas.php">Rotas</a>

                <?php if ($administrador): ?>

                    <a href="../usuario/tela-home-usuario.php">Usuários</a>

                <?php endif; ?>

                <div class="caixa-usuario">

                    <span class="tipo-usuario">
                        <?= $tipoUsuario === 'administrador' ? 'Administrador' : 'Funcionário' ?>
                    </span>

                    <a class="botao-logout" href="../logout/logout.php">
                        Sair
                    </a>

                </div>

            </nav>

        </aside>

    </header>

    <main class="area-principal">

        <section class="cabecalho-sensor topo-lista-trens">

            <h2 class="titulo-sensores">
                Listagem de trens
            </h2>

            <?php if ($administrador): ?>

                <a
                    class="botao-novo-sensor botao-novo-trem"
                    href="cadastro_trem.php"
                >
                    + Novo trem
                </a>

            <?php endif; ?>

        </section>

        <section class="painel-lista-sensores painel-lista-trens">

            <div class="tabela-sensores">

                <div class="cabecalho-tabela colunas-trens <?= $administrador ? 'admin' : '' ?>">

                    <span>ID</span>
                    <span>Nome / Código</span>
                    <span>Modelo / Ano</span>
                    <span>Loc. Inicial / Capacidade</span>

                    <?php if ($administrador): ?>
                        <span>Usuário responsável</span>
                    <?php endif; ?>

                    <span>Status</span>

                    <?php if ($administrador): ?>
                        <span>Ações</span>
                    <?php endif; ?>

                </div>

                <?php
                // Verifica se existem trens cadastrados
                if (mysqli_num_rows($resultado) > 0):
                ?>

                    <?php
                    // Percorre os trens encontrados no banco de dados
                    while ($trem = mysqli_fetch_assoc($resultado)):

                        // Recupera o status do trem
                        $statusBanco = $trem[$col['status']] ?? '';

                        // Define a classe CSS correspondente ao status
                        $classeStatus = isset($rotulosStatus[$statusBanco])
                            ? $statusBanco
                            : 'inativo';

                        // Recupera o texto que será exibido para o status
                        $textoStatus = $rotulosStatus[$statusBanco] ?? $statusBanco;
                    ?>

                        <div class="linha-tabela colunas-trens <?= $administrador ? 'admin' : '' ?>">

                            <span>
                                <?= htmlspecialchars($trem[$col['id']] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </span>

                            <span>
                                <?= htmlspecialchars($trem[$col['nome']] ?? '', ENT_QUOTES, 'UTF-8') ?><br>
                                <small>
                                    <?= htmlspecialchars($trem[$col['codigo']] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </small>
                            </span>

                            <span>
                                <?= htmlspecialchars($trem[$col['modelo']] ?? '', ENT_QUOTES, 'UTF-8') ?><br>
                                <small>
                                    <?= htmlspecialchars($trem[$col['ano']] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </small>
                            </span>

                            <span>
                                <?= htmlspecialchars($trem[$col['local']] ?? '', ENT_QUOTES, 'UTF-8') ?><br>
                                <small>
                                    <?= htmlspecialchars(
                                        (string)(float)($trem[$col['capacidade']] ?? 0),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </small>
                            </span>

                            <?php if ($administrador): ?>

                                <span>
                                    <?= htmlspecialchars(
                                        $trem['usuario_responsavel'] ?? 'Não informado',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                            <?php endif; ?>

                            <span>
                                <span class="status status-trem <?= htmlspecialchars($classeStatus, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($textoStatus, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </span>

                            <?php if ($administrador): ?>

                                <span class="acoes">

                                    <a
                                        href="edicao_trem.php?id=<?= urlencode($trem['id']) ?>"
                                        class="botao-editar"
                                        title="Editar"
                                    >
                                        E
                                    </a>

                                    <a
                                        href="exclusao_trem.php?id=<?= urlencode($trem['id']) ?>"
                                        class="botao-excluir"
                                        title="Excluir"
                                    >
                                        D
                                    </a>

                                </span>

                            <?php endif; ?>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <p class="nenhum-sensor">
                        Nenhum trem cadastrado.
                    </p>

                <?php endif; ?>

            </div>

        </section>

    </main>

</body>
</html>
