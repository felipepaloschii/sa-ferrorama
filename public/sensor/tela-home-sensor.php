
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['tipo'])) {
    header('Location: ../usuario/tela-login.php');
    exit;
}

require_once '../../infra/conexao.php';

$administrador = $_SESSION['tipo'] === 'administrador';

$sql = "SELECT
            sensor.*,
            rotas.nome_rota,
            rotas.codigo AS codigo_rota
        FROM sensor
        LEFT JOIN rotas ON sensor.id_rota = rotas.id_rota
        ORDER BY sensor.id DESC";

$resultado = $conexao->query($sql);

$sensores = [];
$quantidade_ativos = 0;

if ($resultado) {
    while ($sensor = $resultado->fetch_assoc()) {
        $sensores[] = $sensor;

        if (strtolower(trim($sensor['status_inicial'])) === 'ativo') {
            $quantidade_ativos++;
        }
    }
}

function e($valor)
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Listagem de Sensores</title>

    <link rel="stylesheet" href="../../assets/style/style.css">

    <style>
        /* TABELA EXCLUSIVA DE SENSORES */

        .tabela-sensores-grade {
            width: 100%;
            overflow-x: auto;
        }

        .cabecalho-sensores-grade,
        .linha-sensores-grade {
            display: grid !important;
            grid-template-columns: 55px 1.4fr 1fr 1.4fr 1.5fr 0.8fr 75px 85px 80px !important;
            align-items: center !important;
            gap: 12px !important;
            width: 100%;
            min-width: 1250px;
            box-sizing: border-box;
            padding: 14px 16px !important;
            font-size: 13px;
            white-space: nowrap;
        }

        .cabecalho-sensores-grade {
            color: var(--bg-dark);
            background: var(--accent-light);
            border-radius: var(--radius-sm);
            font-weight: 700;
            margin-bottom: 4px;
        }

        .linha-sensores-grade {
            min-height: 68px;
            background: var(--surface-card);
            border-bottom: 1px solid var(--border-color);
            transition: background 0.15s;
        }

        .linha-sensores-grade:hover {
            background: #f8fafc;
        }

        .linha-sensores-grade > span,
        .cabecalho-sensores-grade > span {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .acoes-sensores-grade {
            display: flex !important;
            align-items: center;
            justify-content: flex-start;
            gap: 6px;
            white-space: nowrap;
        }

        .acoes-sensores-grade a {
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .tabela-sensores-grade {
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
            <a class="ativo" href="tela-home-sensor.php">Sensores</a>
            <a href="../trem/lista_trem.php">Trens</a>
            <a href="../rotas/tela-home-rotas.php">Rotas</a>

            <?php if ($administrador): ?>
                <a href="../usuario/tela-home-usuario.php">Usuários</a>
            <?php endif; ?>
        </nav>
    </aside>
</header>

<main class="area-principal">

    <section class="cabecalho-sensor">

        <div>
            <h1 class="titulo-sensores">Listagem de Sensores</h1>

            <p class="descricao-sensores">
                Sensores cadastrados no sistema.
            </p>
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

                        <span><?= e($sensor['id']) ?></span>

                        <span><?= e($sensor['nome_sensor']) ?></span>

                        <span><?= e($sensor['tipo_sensor']) ?></span>

                        <span><?= e($sensor['localizacao']) ?></span>

                        <span>
                            <?php if (!empty($sensor['nome_rota'])): ?>
                                <?= e($sensor['nome_rota']) ?>
                                (<?= e($sensor['codigo_rota']) ?>)
                            <?php else: ?>
                                Sem rota associada
                            <?php endif; ?>
                        </span>

                        <span><?= e($sensor['unidade_medida']) ?></span>

                        <span><?= e($sensor['limite_alerta']) ?></span>

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
                                    href="tela-edit-sensor.php?id=<?= (int) $sensor['id'] ?>"
                                    class="botao-editar"
                                    title="Editar">
                                    E
                                </a>

                                <a
                                    href="tela-excluir-sensor.php?id=<?= (int) $sensor['id'] ?>"
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
