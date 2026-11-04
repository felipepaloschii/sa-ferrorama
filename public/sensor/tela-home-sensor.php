<?php 
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['tipo'])) {
    header('Location: ../usuario/tela-login.php');
    exit;
}

require_once '../../infra/conexao.php';

$quantidade_ativos = 0;
$sensores = [];

$sql = "SELECT * FROM sensor ORDER BY id DESC";
$resultado = $conexao->query($sql);

if ($resultado) {
    while ($sensor = $resultado->fetch_assoc()) {
        $sensores[] = $sensor;
        if (strtolower(trim($sensor['status_inicial'])) === 'ativo') {
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
    <title>Tela de Sensores - PIA Enterprise</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
    <style>
        /* Ajuste específico para garantir o alinhamento lado a lado perfeito da tabela de sensores */
        .cabecalho-tabela, .linha-tabela {
            display: grid;
            grid-template-columns: 60px 1.5fr 1.2fr 1.5fr 1fr 1fr 100px 90px;
            align-items: center;
            padding: 12px 16px;
            font-size: 13px;
            gap: 10px;
        }
    </style>
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

    <aside class="menu-lateral">
        <nav>
            <a href="../../index.php">Dashboard</a>
            <a class="ativo" href="../sensor/tela-home-sensor.php">Sensores</a>
            <a href="lista_trem.php">Trens</a>
            <a href="../rotas/tela-home-rotas.php">Rotas</a>

            <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
                <a href="../usuario/tela-home-usuario.php">Usuários</a>
            <?php endif; ?>
        </nav>
    </aside>
</header>
    <!-- ÁREA PRINCIPAL -->
    <main class="area-principal">

        <section class="cabecalho-sensor" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
            <div class="cartao-metrica" style="min-width: 220px; margin: 0;">
                <h3>Sensores Ativos</h3>
                <div class="linha-metrica">
                    <strong><?php echo $quantidade_ativos; ?></strong>
                </div>
            </div>

            <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
                <a class="botao-primario" href="tela-cadastro-sensor.php">+ Novo Sensor</a>
            <?php endif; ?>
        </section>

        <!-- PAINEL DE LISTAGEM DE SENSORES -->
        <section class="painel-tabela">
            <div class="titulo-cartao">
                <span>Lista de Sensores</span>
            </div>

            <div class="tabela-container">
                <div class="cabecalho-tabela">
                    <span>ID</span>
                    <span>Nome</span>
                    <span>Tipo</span>
                    <span>Localização</span>
                    <span>Unidade</span>
                    <span>Limite</span>
                    <span>Status</span>
                    <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
                        <span style="text-align: right;">Ações</span>
                    <?php endif; ?>
                </div>

                <?php if (count($sensores) > 0): ?>
                    <?php foreach ($sensores as $sensor): ?>
                        <div class="linha-tabela">
                            <span><?php echo htmlspecialchars($sensor['id']); ?></span>
                            <strong><?php echo htmlspecialchars($sensor['nome_sensor']); ?></strong>
                            <span><?php echo htmlspecialchars($sensor['tipo_sensor']); ?></span>
                            <span><?php echo htmlspecialchars($sensor['localizacao']); ?></span>
                            <span><?php echo htmlspecialchars($sensor['unidade_medida']); ?></span>
                            <span><?php echo htmlspecialchars($sensor['limite_alerta']); ?></span>
                            
                            <div>
                                <?php if (strtolower(trim($sensor['status_inicial'])) === 'ativo'): ?>
                                    <span class="badge-status ativo">Ativo</span>
                                <?php else: ?>
                                    <span class="badge-status inativo">Inativo</span>
                                <?php endif; ?>
                            </div>

                            <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
                                <div class="acoes" style="justify-content: flex-end;">
                                    <a href="tela-edit-sensor.php?id=<?php echo $sensor['id']; ?>" class="botao-acao editar" title="Editar">E</a>
                                    <a href="tela-excluir-sensor.php?id=<?php echo $sensor['id']; ?>" class="botao-acao excluir" title="Excluir">D</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="nenhum-sensor" style="padding: 40px; text-align: center; color: var(--text-muted);">Nenhum sensor cadastrado.</p>
                <?php endif; ?>
            </div>
        </section>

    </main>

</body>
</html>