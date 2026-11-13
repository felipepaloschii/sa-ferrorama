<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$erro = '';
$trem = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if (!$id || $id < 1) {
    header('Location: lista_trem.php');
    exit;
}

// Consulta usando LEFT JOIN para buscar o e-mail do usuário responsável cadastrado na tabela usuario
$consulta = mysqli_prepare($conexao, '
    SELECT t.id, t.nome_trem, t.modelo_trem, t.localizacao_i, t.codigo, t.capacidade, t.ano, t.status, t.id_usuario, u.email 
    FROM trem t 
    LEFT JOIN usuario u ON t.id_usuario = u.id_usuario 
    WHERE t.id = ?
');

if ($consulta) {
    mysqli_stmt_bind_param($consulta, 'i', $id);
    mysqli_stmt_execute($consulta);
    $resultadoConsulta = mysqli_stmt_get_result($consulta);
    
    if ($resultadoConsulta && $dadosTrem = mysqli_fetch_assoc($resultadoConsulta)) {
        $trem = [
            'id'          => $dadosTrem['id'],
            'nome'        => $dadosTrem['nome_trem'],
            'modelo'      => $dadosTrem['modelo_trem'],
            'localizacao' => $dadosTrem['localizacao_i'],
            'codigo'      => $dadosTrem['codigo'],
            'capacidade'  => $dadosTrem['capacidade'],
            'ano'         => $dadosTrem['ano'],
            'status'      => $dadosTrem['status'],
            'id_usuario'  => $dadosTrem['id_usuario'],
            'email'       => $dadosTrem['email'] ?? null
        ];
    }
    mysqli_stmt_close($consulta);
}

if (!$trem) {
    header('Location: lista_trem.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($trem['id_usuario']) && $trem['id_usuario'] > 0) {
        $erro = 'Este trem não pode ser excluído pois está atribuído a um usuário responsável.';
    } else {
        $exclusao = mysqli_prepare($conexao, 'DELETE FROM trem WHERE id = ?');
        
        if ($exclusao) {
            mysqli_stmt_bind_param($exclusao, 'i', $id);

            if (mysqli_stmt_execute($exclusao)) {
                mysqli_stmt_close($exclusao);
                header('Location: lista_trem.php');
                exit;
            } else {
                $erro = 'Não foi possível excluir este trem. Verifique se existem rotas ou registros vinculados a ele.';
            }
            mysqli_stmt_close($exclusao);
        } else {
            $erro = 'Erro ao processar a exclusão no banco de dados.';
        }
    }
}

function e($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$rotulosStatus = [
    'operacao'   => 'Em operação',
    'parado'     => 'Parado',
    'manutencao' => 'Manutenção',
    'inativo'    => 'Inativo',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Trem - PIA Enterprise</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
    <script src="../../script/confirmar-exclusao.js" defer></script>
</head>

<body>
    <header class="barra-superior">
        <h1>PIA Enterprise</h1>
        <nav class="navegacao-principal">
            <a href="../../index.php">Dashboard</a>
            <a href="../sensor/tela-home-sensor.php">Sensores</a>
            <a href="lista_trem.php">Trens</a>
            <a class="ativo" href="../rotas/tela-home-rotas.php">Rotas</a>
            <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
                <a href="../usuario/tela-home-usuario.php">Usuários</a>
            <?php endif; ?>
        </nav>
    </header>


    <main class="area-principal">
        <div class="cabecalho-cadastro-sensor">
            <div class="informacoes-pagina">
                <h1 class="titulo-pagina">Excluir trem</h1>
                <p class="descricao-sensores">Confirme os dados do trem antes de prosseguir com a exclusão.</p>
            </div>
            <a href="lista_trem.php" class="botao-voltar-sensores">← Voltar para trens</a>
        </div>

        <section class="painel-excluir-sensor">
            <div class="icone-excluir-sensor" aria-hidden="true">🗑</div>
            <h2>Tem certeza que deseja excluir este trem?</h2>
            <p>Essa ação não poderá ser desfeita.</p>

            <div class="informacoes-sensor-excluir" style="margin: 20px 0; line-height: 1.8;">
                <span><strong>Nome:</strong> <?= e($trem['nome']) ?></span><br>
                <span><strong>Modelo:</strong> <?= e($trem['modelo']) ?></span><br>
                <span><strong>Usuário responsável:</strong> <?= !empty($trem['email']) ? e($trem['email']) : 'Nenhum usuário atribuído' ?></span><br>
                <span><strong>Localização:</strong> <?= e($trem['localizacao']) ?></span><br>
                <span><strong>Código:</strong> <?= e($trem['codigo']) ?></span><br>
                <span><strong>Capacidade:</strong> <?= e($trem['capacidade']) ?></span><br>
                <span><strong>Ano:</strong> <?= e($trem['ano']) ?></span><br>
                <span><strong>Status:</strong> <?= e($rotulosStatus[$trem['status']] ?? $trem['status']) ?></span>
            </div>

            <?php if ($erro !== ''): ?>
                <div class="aviso-cadastro falha" style="margin-bottom: 20px; padding: 12px; background: var(--danger-bg, #fee2e2); color: var(--danger, #ef4444); border-radius: var(--radius-sm, 6px);">
                    <?= e($erro) ?>
                </div>
            <?php endif; ?>

            <div class="botoes-excluir-sensor">
                <form method="post" action="exclusao_trem.php?id=<?= e($id) ?>">
                    <input type="hidden" name="id" value="<?= e($id) ?>">
                    <button class="botao-confirmar-exclusao" type="submit">Excluir trem</button>
                    <a href="lista_trem.php" class="botao-voltar-trem" style="margin-left: 10px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; height: 38px; padding: 0 16px;">Cancelar</a>
                </form>
            </div>
        </section>
    </main>
</body>

</html>