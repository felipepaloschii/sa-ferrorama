

<?php

// require_once inclui o arquivo de verificacao de administrador apenas uma vez
// ele verifica se o usuario esta conectado e se possui permissao pra excluir usuarios
require_once '../../infra/verificar-admin.php';

// inclui o arquivo responsavel pela conexao com o banco de dados
require_once '../../infra/conexao.php';

// filter_input recebe e valida um dado enviado pelo formulario ou pela URL
// INPUT_GET indica que o id sera recebido pela URL
// FILTER_VALIDATE_INT verifica se o valor informado e um numero inteiro valido
// se o id nao for valido, a variavel recebe false
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// cria uma variavel vazia pra armazenar uma possivel mensagem de erro
$erro = '';

// null indica que nenhum usuario foi encontrado ate o momento
$usuario = null;

// $_SERVER['REQUEST_METHOD'] informa qual metodo foi utilizado na requisicao
// === compara o valor e o tipo
// POST indica que o formulario foi enviado pelo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // recebe novamente o id enviado pelo formulario
    // INPUT_POST indica que o dado vem de um formulario enviado pelo metodo POST
    // FILTER_VALIDATE_INT verifica se o id e um numero inteiro valido
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

// ! verifica se a condicao e falsa
// nesse caso, !$id verifica se o id nao possui um valor valido
// || significa OU, entao tambem verifica se o id e menor que 1
if (!$id || $id < 1) {

    // header redireciona o usuario pra pagina de listagem
    header('Location: tela-home-usuario.php');

    // exit encerra a execucao pra impedir que o restante do codigo continue
    exit;
}

// mysqli_prepare prepara uma consulta SQL pra receber valores com seguranca
// o ? funciona como um marcador que sera preenchido posteriormente
// SELECT busca os dados do usuario que possui o id informado
$consulta = mysqli_prepare(
    $conexao,
    'SELECT id_usuario, email, tipo FROM usuario WHERE id_usuario = ?'
);

// mysqli_stmt_bind_param associa o valor do id ao marcador ? da consulta
// i indica que o valor enviado e um numero inteiro
mysqli_stmt_bind_param($consulta, 'i', $id);

// executa a consulta preparada no banco de dados
mysqli_stmt_execute($consulta);

// mysqli_stmt_bind_result associa as colunas retornadas pela consulta as variaveis
// cada coluna do SELECT sera armazenada na variavel correspondente
mysqli_stmt_bind_result(
    $consulta,
    $id_usuario,
    $email,
    $tipo
);

// mysqli_stmt_fetch tenta buscar uma linha do resultado
// if verifica se encontrou um usuario com o id informado
if (mysqli_stmt_fetch($consulta)) {

    // cria um array associativo pra guardar os dados encontrados
    // cada informacao recebe uma chave que facilita o acesso posteriormente
    $usuario = [
        'id_usuario' => $id_usuario,
        'email' => $email,
        'tipo' => $tipo
    ];
}

// mysqli_stmt_close encerra a consulta e libera os recursos utilizados
mysqli_stmt_close($consulta);

// verifica se nenhum usuario foi encontrado
// ! antes da variavel verifica se ela esta sem um valor valido
if (!$usuario) {

    // volta pra listagem porque nao existe usuario correspondente ao id
    header('Location: tela-home-usuario.php');

    // encerra a execucao depois do redirecionamento
    exit;
}

// verifica novamente se o formulario foi enviado pelo metodo POST
// essa condicao impede que a exclusao aconteca apenas ao abrir a pagina
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // prepara o comando SQL responsavel por excluir o usuario
    // DELETE FROM indica que um registro sera removido da tabela
    // WHERE limita a exclusao ao usuario que possui o id informado
    $exclusao = mysqli_prepare(
        $conexao,
        'DELETE FROM usuario WHERE id_usuario = ?'
    );

    // associa o id ao marcador da consulta
    // i informa que o valor e um numero inteiro
    mysqli_stmt_bind_param(
        $exclusao,
        'i',
        $id
    );

    // executa o comando de exclusao no banco
    // se a execucao funcionar, entra no bloco abaixo
    if (mysqli_stmt_execute($exclusao)) {

        // encerra a consulta e libera os recursos utilizados
        mysqli_stmt_close($exclusao);

        // redireciona pra listagem depois da exclusao
        header('Location: tela-home-usuario.php');

        // encerra o codigo depois do redirecionamento
        exit;
    }

    // se a exclusao falhar, guarda uma mensagem pra ser exibida na pagina
    $erro = 'Não foi possível excluir este usuário.';

    // encerra a consulta mesmo quando a exclusao nao funciona
    mysqli_stmt_close($exclusao);
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

                    🗑

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
                        <?= e($usuario['id_usuario']) ?>
                    </span>

                    <br>

                    <span>
                        E-mail:
                    <?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>                    
                </span>

                    <br>

                    <span>
                        Tipo:

                        <?php

                        if ($usuario['tipo'] === 'administrador') {

                            echo 'Administrador';

                        } elseif ($usuario['tipo'] === 'funcionario') {

                            echo 'Funcionário';

                        } else {

                            echo e($usuario['tipo']);
                        }

                        ?>

                    </span>

                </div>

                <?php if ($erro !== ''): ?>

                    <p class="erro-excluir-sensor">
                        <?= e($erro) ?>
                    </p>

                <?php endif; ?>

                <div class="botoes-excluir-sensor">

                    <form
                        method="post"
                        action="tela-excluir-usuario.php">

                        <input
                            type="hidden"
                            name="id"
                            value="<?= e($id) ?>">

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
