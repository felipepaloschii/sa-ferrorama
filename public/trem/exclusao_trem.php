
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

// Busca os dados do trem e o usuário responsável
$consulta = $conexao->prepare(
    'SELECT
        t.id,
        t.nome_trem,
        t.modelo_trem,
        t.localizacao_i,
        t.codigo,
        t.capacidade,
        t.ano,
        t.status,
        t.id_usuario,
        u.email
    FROM trem t
    LEFT JOIN usuario u ON t.id_usuario = u.id_usuario
    WHERE t.id = ?'
);

if (!$consulta) {
    die('Erro ao consultar o trem.');
}

$consulta->bind_param('i', $id);
$consulta->execute();

$resultado = $consulta->get_result();
$dadosTrem = $resultado->fetch_assoc();

$consulta->close();

if (!$dadosTrem) {
    header('Location: lista_trem.php');
    exit;
}

$trem = [
    'id' => $dadosTrem['id'],
    'nome' => $dadosTrem['nome_trem'],
    'modelo' => $dadosTrem['modelo_trem'],
    'localizacao' => $dadosTrem['localizacao_i'],
    'codigo' => $dadosTrem['codigo'],
    'capacidade' => $dadosTrem['capacidade'],
    'ano' => $dadosTrem['ano'],
    'status' => $dadosTrem['status'],
    'id_usuario' => $dadosTrem['id_usuario'],
    'email' => $dadosTrem['email']
];

// Exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($trem['id_usuario'])) {

        $erro = 'Este trem não pode ser excluído, pois está atribuído a um usuário responsável.';

    } else {

        $exclusao = $conexao->prepare(
            'DELETE FROM trem WHERE id = ?'
        );

        if ($exclusao) {

            $exclusao->bind_param('i', $id);

            if ($exclusao->execute() && $exclusao->affected_rows > 0) {

                $exclusao->close();

                header('Location: lista_trem.php');
                exit;

            } else {

                $erro = 'Não foi possível excluir este trem. Verifique se existem rotas ou registros vinculados a ele.';
            }

            $exclusao->close();

        } else {

            $erro = 'Erro ao processar a exclusão no banco de dados.';
        }
    }
}

function e($valor)
{
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

$rotulosStatus = [
    'operacao' => 'Em operação',
    'parado' => 'Parado',
    'manutencao' => 'Manutenção',
    'inativo' => 'Inativo'
];

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Excluir trem</title>

    <link
        rel="stylesheet"
        href="../../assets/style/style.css">
</head>

<body>

    <header class="barra-superior">

        <h1>PIA Enterprise</h1>

        <aside class="menu-lateral">
            <nav>
                <a href="../../index.php">Dashboard</a>

                <a href="../sensor/tela-home-sensor.php">
                    Sensores
                </a>

                <a class="ativo" href="lista_trem.php">
                    Trens
                </a>

                <a href="../rotas/tela-home-rotas.php">
                    Rotas
                </a>

                <?php if ($_SESSION['tipo'] === 'administrador'): ?>
                    <a href="../usuario/tela-home-usuario.php">
                        Usuários
                    </a>
                <?php endif; ?>
            </nav>
        </aside>

    </header>

    <main class="area-principal">

        <section class="cabecalho-cadastro-sensor">

            <div class="informacoes-pagina">

                <h1 class="titulo-pagina">
                    Excluir trem
                </h1>

                <p class="descricao-sensores">
                    Confirme os dados do trem antes de prosseguir com a exclusão.
                </p>

            </div>

            <a
                href="lista_trem.php"
                class="botao-voltar-sensores">

                Voltar para trens

            </a>

        </section>

        <section class="painel-excluir-sensor">

        

            <h2>
                Tem certeza que deseja excluir este trem?
            </h2>

            <p>
                Essa ação não poderá ser desfeita.
            </p>

            <div class="informacoes-sensor-excluir">

                <div>
                    <strong>Nome:</strong>
                    <?= e($trem['nome']) ?>
                </div>

                <div>
                    <strong>Modelo:</strong>
                    <?= e($trem['modelo']) ?>
                </div>

                <div>
                    <strong>Usuário responsável:</strong>
                    <?= !empty($trem['email'])
                        ? e($trem['email'])
                        : 'Nenhum usuário atribuído' ?>
                </div>

                <div>
                    <strong>Localização:</strong>
                    <?= e($trem['localizacao']) ?>
                </div>

                <div>
                    <strong>Código:</strong>
                    <?= e($trem['codigo']) ?>
                </div>

                <div>
                    <strong>Capacidade:</strong>
                    <?= e($trem['capacidade']) ?>
                </div>

                <div>
                    <strong>Ano:</strong>
                    <?= e($trem['ano']) ?>
                </div>

                <div>
                    <strong>Status:</strong>
                    <?= e(
                        $rotulosStatus[$trem['status']]
                        ?? $trem['status']
                    ) ?>
                </div>

            </div>

            <?php if ($erro !== ''): ?>
                <div class="erro-excluir-sensor">
                    <?= e($erro) ?>
                </div>
            <?php endif; ?>

            <div class="botoes-excluir-sensor">

                <form
                    method="POST"
                    action="exclusao_trem.php">

                    <input
                        type="hidden"
                        name="id"
                        value="<?= e($id) ?>">

                    <button
                        class="botao-confirmar-exclusao"
                        type="submit">

                        Excluir trem

                    </button>

                </form>

                <a
                    href="lista_trem.php"
                    class="botao-cancelar-cadastro">

                    Cancelar

                </a>

            </div>

        </section>

    </main>

</body>
</html>
