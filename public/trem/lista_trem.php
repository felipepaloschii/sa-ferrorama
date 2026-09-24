<?php
include '../../infra/conexao.php';

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
</head>

<body>
    <header class="barra-superior">
        <h1>PIA Enterprise</h1>
    </header>

    <aside class="menu-lateral">
        <nav>
            <a href="../../index.php">Dashboard</a>
            <a href="tela-home-sensor.php">Sensores</a>
            <a class="ativo" href="lista_trem.php">Trens</a>
            <a href="#">Rotas</a>
        </nav>
    </aside>

    <main class="area-principal">
        <section class="cabecalho-sensor topo-lista-trens">
            <h2 class="titulo-sensores">Listagem de trens</h2>
            <a class="botao-novo-sensor botao-novo-trem" href="cadastro_trem.php">+ Novo Trem</a>
        </section>

        <section class="painel-lista-sensores painel-lista-trens">
            <div class="tabela-sensores">
                <div class="cabecalho-tabela colunas-trens">
                    <span>ID</span>
                    <span>Nome</span>
                    <span>Modelo</span>
                    <span>Loc. Inicial</span>
                    <span>Ano</span>
                    <span>Capacidade</span>
                    <span>Status</span>
                    <span>Código</span>
                    <span>Ações</span>
                </div>

                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($trem = mysqli_fetch_assoc($resultado)): ?>
                        <?php
                        $statusBanco   = $trem[$col['status']] ?? '';
                        $classeStatus  = isset($rotulosStatus[$statusBanco]) ? $statusBanco : 'inativo';
                        $textoStatus   = $rotulosStatus[$statusBanco] ?? $statusBanco;
                        ?>
                        <div class="linha-tabela colunas-trens">
                            <span><?= htmlspecialchars(($trem[$col['id']] ?? '')) ?></span>
                            <span><?= htmlspecialchars(($trem[$col['nome']] ?? '')) ?></span>
                            <span><?= htmlspecialchars(($trem[$col['modelo']] ?? '')) ?></span>
                            <span><?= htmlspecialchars(($trem[$col['local']] ?? '')) ?></span>
                            <span><?= htmlspecialchars(($trem[$col['ano']] ?? '')) ?></span>
                            <span><?= htmlspecialchars((string)(float)($trem[$col['capacidade']] ?? 0)) ?></span>
                            <span>
                                <span class="status status-trem <?= $classeStatus ?>">
                                    <?= htmlspecialchars($textoStatus) ?>
                                </span>
                            </span>
                            <span><?= htmlspecialchars(($trem[$col['codigo']] ?? '')) ?></span>
                            <span class="acoes">
                                <button type="button" onclick="window.location.href='public/edicao_trem.php?id=<?php echo $trem['id']; ?>'">Editar</button>
                                <button type="button" onclick="if (confirm('Tem certeza que deseja excluir este trem?')) { window.location.href='public/exclusao_trem.php?id=<?php echo $trem['id']; ?>'; }">Excluir</button>
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