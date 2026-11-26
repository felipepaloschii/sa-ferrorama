
<?php

// session_status() verifica se já existe uma sessão ativa no PHP
// Se não existir, session_start() inicia uma nova sessão para conseguir acessar os dados do usuário
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// isset() verifica se uma variável existe e não possui valor null
// Aqui estamos conferindo se o usuário possui um ID e um tipo de conta salvos na sessão
// O || significa OU, então se uma das duas informações estiver faltando, o acesso será bloqueado
if (
    !isset($_SESSION['id_usuario']) ||
    !isset($_SESSION['tipo'])
) {
    // header() redireciona o usuário para outra página
    // Nesse caso, volta para a tela de login porque não existe uma sessão válida
    header('Location: ../usuario/tela-login.php');
    exit;
}

// require_once inclui o arquivo de conexão com o banco de dados apenas uma vez
// O ../../ volta duas pastas para encontrar a pasta infra
require_once '../../infra/conexao.php';

// $_SESSION['tipo'] guarda o tipo de conta do usuário que está conectado
// === compara se o valor é exatamente igual a 'administrador'
// O resultado será true ou false, dependendo do tipo de usuário
$administrador = $_SESSION['tipo'] === 'administrador';

// Guarda o tipo de usuário em uma variável para utilizar no cabeçalho
// ?? significa que se o valor não existir ou for null, será utilizado um texto vazio
$tipoUsuario = $_SESSION['tipo'] ?? '';

// Guarda o nome da tabela do banco de dados que será utilizada na consulta
$tabela = 'trem';

// Cria um array com os nomes das colunas da tabela trem
// Isso facilita acessar os campos sem precisar escrever o nome da coluna várias vezes
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

// Cria um array para transformar os valores do status do banco em textos mais fáceis de entender
// Por exemplo, quando o banco retorna 'operacao', a página mostra 'Em operação'
$rotulosStatus = [
    'operacao'   => 'Em operação',
    'parado'     => 'Parado',
    'manutencao' => 'Manutenção',
    'inativo'    => 'Inativo',
];

// SELECT busca as informações dos trens no banco de dados
// trem.* significa que serão selecionadas todas as colunas da tabela trem
// LEFT JOIN permite buscar também o email do usuário responsável pelo trem
// Mesmo que o trem não tenha um usuário responsável, ele continuará aparecendo na listagem
// AS usuario_responsavel cria um apelido para o campo usuario.email
// ON define a condição que relaciona as duas tabelas usando o ID do usuário
// ORDER BY trem.id organiza os resultados pelo ID do trem
$sql = "SELECT trem.*, usuario.email AS usuario_responsavel
        FROM `$tabela` AS trem
        LEFT JOIN usuario
            ON trem.id_usuario = usuario.id_usuario
        ORDER BY trem.id";

// mysqli_query() envia a consulta SQL para o banco de dados através da conexão
// O resultado da consulta fica guardado na variável $resultado
$resultado = mysqli_query($conexao, $sql);

// Se a consulta apresentar algum erro, die() interrompe a página e mostra a mensagem
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
            </nav>

            <div class="caixa-usuario">

                <span class="tipo-usuario">
                    <?= $tipoUsuario === 'administrador' ? 'Administrador' : 'Funcionário' ?>
                </span>

                <a class="botao-logout" href="../logout/logout.php">
                    Sair
                </a>

            </div>
        </aside>
    </header>

    <main class="area-principal">

        <section class="cabecalho-sensor topo-lista-trens">

            <h2 class="titulo-sensores">Listagem de trens</h2>

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
                // mysqli_num_rows() conta quantas linhas foram retornadas pela consulta
                // Se for maior que zero, significa que existe pelo menos um trem cadastrado
                if (mysqli_num_rows($resultado) > 0):
                ?>

                    <?php
                    // mysqli_fetch_assoc() pega uma linha do resultado e transforma em um array associativo
                    // Isso permite acessar cada informação pelo nome da coluna, como nome_trem ou status
                    // while repete o bloco enquanto ainda existirem trens para mostrar
                    while ($trem = mysqli_fetch_assoc($resultado)):
                    ?>

                        <?php
                        // Pega o status do trem utilizando a chave correspondente no array $col
                        // ?? significa que se o campo não existir ou estiver null, será utilizado um texto vazio
                        $statusBanco = $trem[$col['status']] ?? '';

                        // isset() verifica se o status existe dentro do array de rótulos
                        // Se existir, utiliza o próprio status como classe CSS
                        // Caso contrário, utiliza 'inativo'
                        $classeStatus = isset($rotulosStatus[$statusBanco])
                            ? $statusBanco
                            : 'inativo';

                        // Busca o texto correspondente ao status
                        // ?? utiliza o próprio valor do banco caso não exista um rótulo cadastrado
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
