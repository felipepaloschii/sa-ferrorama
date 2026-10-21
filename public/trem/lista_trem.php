<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (
    !isset($_SESSION['id_usuario']) ||
    !isset($_SESSION['tipo'])
) {
    header('Location: ../usuario/tela-login.php');
    exit;
}

require_once '../../infra/conexao.php';


$tabela = 'trem';

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

$rotulosStatus = [
    'operacao'   => 'Em operação',
    'parado'     => 'Parado',
    'manutencao' => 'Manutenção',
    'inativo'    => 'Inativo',
];

$resultado = mysqli_query($conexao, "SELECT * FROM `$tabela` ORDER BY `{$col['id']}`");
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Trens</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
    <link rel="stylesheet" href="../../assets/style/lista_trem.css">

    <style>
        .colunas-trens {
    display: grid;
    grid-template-columns:
        60px
        minmax(130px, 1.3fr)
        minmax(130px, 1.2fr)
        minmax(150px, 1.4fr)
        minmax(110px, 0.9fr)
        minmax(140px, 1fr);
    align-items: center;
    gap: 12px;
}

.cabecalho-tabela.colunas-trens {
    text-align: center;
}

.linha-tabela.colunas-trens {
    min-height: 82px;
    text-align: center;
}

.linha-tabela.colunas-trens > span {
    min-width: 0;
    overflow-wrap: anywhere;
}

.linha-tabela.colunas-trens .acoes {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.linha-tabela.colunas-trens .botao-editar,
.linha-tabela.colunas-trens .botao-excluir {
    margin: 0;
    white-space: nowrap;
    cursor: pointer;
}

@media (max-width: 900px) {
    .colunas-trens {
        min-width: 850px;
    }

    .painel-lista-trens {
        overflow-x: auto;
    }
}
    </style>
</head>

<body>
    <header class="barra-superior">
        <h1>PIA Enterprise</h1>
    </header>

    <aside class="menu-lateral">
        <nav>
            <a href="../../index.php">Dashboard</a>
            <a href="../sensor/tela-home-sensor.php">Sensores</a>
            <a class="ativo" href="lista_trem.php">Trens</a>
            <a href="../rotas/tela-home-rotas.php">Rotas</a>
            <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>

            <a href="../usuario/tela-home-usuario.php">Usuários</a>

            <?php endif; ?>
        </nav>
    </aside>

    <main class="area-principal">
        <section class="cabecalho-sensor topo-lista-trens">
            <h2 class="titulo-sensores">Listagem de trens</h2>
            <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
            <a class="botao-novo-sensor botao-novo-trem" href="cadastro_trem.php">+ Novo Trem</a>
            <?php endif; ?>
        </section>

        <section class="painel-lista-sensores painel-lista-trens">
            <div class="tabela-sensores">
                <div class="cabecalho-tabela colunas-trens">
    <span>ID</span>
    <span>Nome / Código</span>
    <span>Modelo / Ano</span>
    <span>Loc. Inicial / Capacidade</span>

    <span>Status</span>

    <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
    <span>Ações</span>
    <?php endif; ?>
        
</div>

                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($trem = mysqli_fetch_assoc($resultado)): ?>
                        <?php
                        $statusBanco   = $trem[$col['status']] ?? '';
                        $classeStatus  = isset($rotulosStatus[$statusBanco]) ? $statusBanco : 'inativo';
                        $textoStatus   = $rotulosStatus[$statusBanco] ?? $statusBanco;
                        ?>
                       <
<div class="linha-tabela colunas-trens">

    <span>
        <?= htmlspecialchars($trem[$col['id']] ?? '') ?>
    </span>

    <span>
        <?= htmlspecialchars($trem[$col['nome']] ?? '') ?><br>
        <small><?= htmlspecialchars($trem[$col['codigo']] ?? '') ?></small>
    </span>

    <span>
        <?= htmlspecialchars($trem[$col['modelo']] ?? '') ?><br>
        <small><?= htmlspecialchars($trem[$col['ano']] ?? '') ?></small>
    </span>

    <span>
        <?= htmlspecialchars($trem[$col['local']] ?? '') ?><br>
        <small><?= htmlspecialchars((string)(float)($trem[$col['capacidade']] ?? 0)) ?></small>
    </span>

    <span>
        <span class="status status-trem <?= htmlspecialchars($classeStatus) ?>">
            <?= htmlspecialchars($textoStatus) ?>
        </span>
    </span>

    <?php if ($administrador): ?>
        <span class="acoes">

            <button
                type="button"
                class="botao-editar"
                onclick="window.location.href='edicao_trem.php?id=<?= (int) $trem['id'] ?>'">
                E
            </button>

            <button
                type="button"
                class="botao-excluir"
                onclick="if (confirm('Tem certeza que deseja excluir este trem?')) {
                    window.location.href='exclusao_trem.php?id=<?= (int) $trem['id'] ?>';
                }">
                D
            </button>

        </span>
    <?php endif; ?>

</div>

<?php endwhile; ?>

                <?php else: ?>
                    <p class="nenhum-sensor">Nenhum trem cadastrado.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>

</html>