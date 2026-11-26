<?php

// inclui o arquivo que verifica se o usuario esta conectado e e administrador
require_once '../../infra/verificar-admin.php';

// inclui o arquivo de conexao com o banco de dados
require_once '../../infra/conexao.php';

// cria uma variavel vazia pra guardar mensagens de erro
$erro = '';

// verifica se o id foi enviado pela url ou pelo formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // recebe o id enviado pelo formulario
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

} else {

    // recebe o id enviado pela url
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
}

// verifica se o id e valido
if (!$id || $id <= 0) {

    // volta pra listagem se o id for invalido
    header('Location: tela-home-usuario.php');
    exit;
}

// consulta os dados do usuario selecionado
$sql = "SELECT * FROM usuario WHERE id_usuario = ?";

// prepara a consulta pra receber o id com seguranca
$stmt = $conexao->prepare($sql);

// verifica se a consulta foi preparada corretamente
if (!$stmt) {
    die('Erro ao preparar a consulta do usuário.');
}

// associa o id ao parametro da consulta
// i indica que o valor e inteiro
$stmt->bind_param("i", $id);

// executa a consulta no banco de dados
$stmt->execute();

// recebe o resultado da consulta
$resultado = $stmt->get_result();

// verifica se o usuario existe
if ($resultado->num_rows == 0) {

    // encerra a consulta
    $stmt->close();

    // volta pra listagem se o usuario nao existir
    header('Location: tela-home-usuario.php');
    exit;
}

// guarda os dados do usuario em um array
$usuario = $resultado->fetch_assoc();

// encerra a consulta
$stmt->close();

// monta o endereco completo pra mostrar na tela
$partesEndereco = [];

if (!empty($usuario['logradouro'])) {
    $partesEndereco[] = $usuario['logradouro'];
}

if (!empty($usuario['bairro'])) {
    $partesEndereco[] = $usuario['bairro'];
}

if (!empty($usuario['cidade'])) {
    $cidadeUf = $usuario['cidade'];

    if (!empty($usuario['uf'])) {
        $cidadeUf .= ' - ' . $usuario['uf'];
    }

    $partesEndereco[] = $cidadeUf;
}

$enderecoCompleto = $partesEndereco ? implode(', ', $partesEndereco) : 'Não informado';

// verifica se o formulario foi enviado pelo metodo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // verifica se o administrador esta tentando excluir a propria conta
    if ((int) $id === (int) $_SESSION['id_usuario']) {

        // impede a exclusao da propria conta
        $erro = 'Você não pode excluir sua própria conta.';

    } else {

        // prepara o comando SQL pra excluir o usuario pelo id
        $exclusao = $conexao->prepare(
            'DELETE FROM usuario WHERE id_usuario = ?'
        );

        // verifica se a consulta foi preparada corretamente
        if ($exclusao) {

            // associa o id ao marcador da consulta
            // i indica que o valor e um numero inteiro
            $exclusao->bind_param("i", $id);

            // executa a exclusao no banco de dados
            if ($exclusao->execute()) {

                // verifica se algum registro foi excluido
                if ($exclusao->affected_rows > 0) {

                    // encerra a consulta
                    $exclusao->close();

                    // redireciona pra listagem depois da exclusao
                    header('Location: tela-home-usuario.php');
                    exit;
                }
            }

            // encerra a consulta e libera os recursos
            $exclusao->close();

            // informa que nao foi possivel excluir o usuario
            $erro = 'Não foi possível excluir este usuário.';

        } else {

            // informa que ocorreu um erro ao preparar a exclusao
            $erro = 'Erro ao preparar a exclusão do usuário.';
        }
    }
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Excluir usuário</title>

    <link
        rel="stylesheet"
        href="../../assets/style/style.css">

</head>

<body>

    <div class="estrutura-dashboard">

        <header class="barra-superior">

            <h1>PIA Enterprise</h1>

            <aside class="menu-lateral">

                <nav>

                    <a href="../../index.php">Dashboard</a>
                    <a href="../sensor/tela-home-sensor.php">Sensores</a>
                    <a href="../trem/lista_trem.php">Trens</a>
                    <a href="../rotas/tela-home-rotas.php">Rotas</a>

                    <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
                        <a class="ativo" href="tela-home-usuario.php">Usuários</a>
                    <?php endif; ?>

                </nav>

            </aside>

        </header>

        <main class="area-principal">

            <section class="cabecalho-cadastro-sensor">

                <div class="informacoes-pagina">

                    <h1 class="titulo-pagina">
                        Excluir usuário
                    </h1>

                    <p class="descricao-sensores">
                        Confirme a exclusão do usuário selecionado.
                    </p>

                </div>

                <button
                    type="button"
                    class="botao-voltar-sensores"
                    onclick="window.location.href='tela-home-usuario.php'">

                    Voltar para usuários

                </button>

            </section>

            <section class="painel-excluir-sensor">

                <div
                    class="icone-excluir-sensor"
                    aria-hidden="true">

            

                </div>

                <h2>
                    Tem certeza que deseja excluir este usuário?
                </h2>

                <p>
                    Essa ação não poderá ser desfeita.
                </p>

                <div class="informacoes-sensor-excluir">

                    <span>
                        ID:
                        <?= htmlspecialchars((string) $usuario['id_usuario'], ENT_QUOTES, 'UTF-8') ?>
                    </span>

                    <br>

                    <span>
                        E-mail:
                        <?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>
                    </span>

                    <br>

                    <span>
                        CEP:
                        <?= htmlspecialchars($usuario['cep'] ?? 'Não informado', ENT_QUOTES, 'UTF-8') ?>
                    </span>

                    <br>

                    <span>
                        Endereço:
                        <?= htmlspecialchars($enderecoCompleto, ENT_QUOTES, 'UTF-8') ?>
                    </span>

                    <br>

                    <span>
                        Tipo:

                        <?php

                        // verifica o tipo de usuario pra mostrar o nome correspondente
                        if ($usuario['tipo'] === 'administrador') {

                            echo 'Administrador';

                        } elseif ($usuario['tipo'] === 'funcionario') {

                            echo 'Funcionário';

                        } else {

                            // mostra o tipo caso seja diferente dos valores conhecidos
                            echo htmlspecialchars($usuario['tipo'], ENT_QUOTES, 'UTF-8');
                        }

                        ?>

                    </span>

                </div>

                <?php if ($erro !== ''): ?>

                    <p class="erro-excluir-sensor">
                        <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                    </p>

                <?php endif; ?>

                <div class="botoes-excluir-sensor">

                    <form
                        method="post"
                        action="tela-excluir-usuario.php">

                        <input
                            type="hidden"
                            name="id"
                            value="<?= htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') ?>">

                        <button
                            class="botao-confirmar-exclusao"
                            type="submit">

                            Excluir usuário

                        </button>

                    </form>

                    <button
                        type="button"
                        class="botao-cancelar-cadastro"
                        onclick="window.location.href='tela-home-usuario.php'">

                        Cancelar

                    </button>

                </div>

            </section>

        </main>

    </div>

</body>

</html>