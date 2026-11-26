
<?php

// Inicia a sessão caso ainda não esteja ativa

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verifica se o usuário logado é administrador e conecta ao banco de dados

require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

// filter_input() lê um valor de fora (URL ou formulário) e valida.
// INPUT_GET pega o valor enviado pela URL (?id=5).
// FILTER_VALIDATE_INT verifica se o valor é um número inteiro.

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Variáveis para mensagens de erro

$erro = '';
$trem = null;

// Recupera o ID enviado pelo formulário.
// Se o formulário for enviado por POST, o ID vem do campo escondido.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

// Se o ID for inválido ou menor que 1, volta para a lista.

if (!$id || $id < 1) {
    header('Location: lista_trem.php');
    exit;
}

// Busca os dados do trem e o e-mail do usuário responsável.
// LEFT JOIN permite encontrar o trem mesmo sem usuário associado.

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

// Se não conseguiu preparar a consulta, interrompe o código.

if (!$consulta) {
    die('Erro ao consultar o trem.');
}

// Associa o ID ao ?.
// O "i" significa inteiro.

$consulta->bind_param('i', $id);

// Executa a consulta.

$consulta->execute();

// Recupera os dados encontrados.

$resultado = $consulta->get_result();
$dadosTrem = $resultado->fetch_assoc();

// Libera os recursos da consulta.

$consulta->close();

// Se não encontrou o trem, volta para a lista.

if (!$dadosTrem) {
    header('Location: lista_trem.php');
    exit;
}

// Organiza os dados em um array mais simples para utilizar no HTML.

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

// A exclusão só acontece quando o formulário é enviado por POST.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verifica se existem rotas associadas ao trem.
    // A tabela rotas utiliza id_trem para relacionar uma rota ao trem.
    // Se existir alguma rota, a exclusão será bloqueada.

    $verificaRotas = $conexao->prepare(
        'SELECT id_rota FROM rotas WHERE id_trem = ? LIMIT 1'
    );

    // Associa o ID do trem à consulta.

    $verificaRotas->bind_param('i', $id);

    // Executa a consulta.

    $verificaRotas->execute();

    // Guarda o resultado para verificar se existe alguma rota.

    $verificaRotas->store_result();

    // Se retornar alguma linha, existe uma rota associada.

    $possuiRotas = $verificaRotas->num_rows > 0;

    // Libera os recursos da consulta.

    $verificaRotas->close();

    // Se o trem possuir rotas, impede a exclusão.

    if ($possuiRotas) {

        $erro = 'Este trem não pode ser excluído, pois possui uma ou mais rotas associadas.';

    } else {

        // Se não possuir rotas, prepara o comando para excluir o trem.
        // O WHERE id = ? garante que somente o trem selecionado será excluído.

        $exclusao = $conexao->prepare(
            'DELETE FROM trem WHERE id = ?'
        );

        // Verifica se o comando foi preparado corretamente.

        if ($exclusao) {

            // Associa o ID ao comando DELETE.

            $exclusao->bind_param('i', $id);

            // Executa a exclusão e verifica se algum registro foi removido.

            if ($exclusao->execute() && $exclusao->affected_rows > 0) {

                // Libera os recursos antes de redirecionar.

                $exclusao->close();

                // Volta para a lista de trens após a exclusão.

                header('Location: lista_trem.php');
                exit;

            } else {

                // Exibe uma mensagem caso não consiga excluir o registro.

                $erro = 'Não foi possível excluir este trem. Verifique se existem registros vinculados a ele.';
            }

            // Libera os recursos do comando DELETE.

            $exclusao->close();

        } else {

            // Exibe uma mensagem caso não consiga preparar a exclusão.

            $erro = 'Erro ao processar a exclusão no banco de dados.';
        }
    }
}

// O banco guarda o status como um código técnico.
// Este array transforma os códigos em textos que aparecem na tela.

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
                    <?= htmlspecialchars($trem['nome'], ENT_QUOTES, 'UTF-8') ?>
                </div>

                <div>
                    <strong>Modelo:</strong>
                    <?= htmlspecialchars($trem['modelo'], ENT_QUOTES, 'UTF-8') ?>
                </div>

                <div>
                    <strong>Usuário responsável:</strong>
                    <?= !empty($trem['email'])
                        ? htmlspecialchars($trem['email'], ENT_QUOTES, 'UTF-8')
                        : 'Nenhum usuário atribuído' ?>
                </div>

                <div>
                    <strong>Localização:</strong>
                    <?= htmlspecialchars($trem['localizacao'], ENT_QUOTES, 'UTF-8') ?>
                </div>

                <div>
                    <strong>Código:</strong>
                    <?= htmlspecialchars($trem['codigo'], ENT_QUOTES, 'UTF-8') ?>
                </div>

                <div>
                    <strong>Capacidade:</strong>
                    <?= htmlspecialchars((string) $trem['capacidade'], ENT_QUOTES, 'UTF-8') ?>
                </div>

                <div>
                    <strong>Ano:</strong>
                    <?= htmlspecialchars((string) $trem['ano'], ENT_QUOTES, 'UTF-8') ?>
                </div>

                <div>
                    <strong>Status:</strong>
                    <?= htmlspecialchars(
                        $rotulosStatus[$trem['status']] ?? $trem['status'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>

            </div>

            <?php if ($erro !== ''): ?>
                <div class="erro-excluir-sensor">
                    <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <div class="botoes-excluir-sensor">

                <form
                method="POST"
                action="exclusao_trem.php">
                    <input
                        type="hidden"
                        name="id"
                        value="<?= htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') ?>">

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
