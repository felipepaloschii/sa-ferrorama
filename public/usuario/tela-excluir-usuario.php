<?php

include '../../infra/conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$erro = '';
$usuario = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if (!$id || $id < 1) {
    header('Location: tela-home-usuario.php');
    exit;
}

$consulta = mysqli_prepare(
    $conexao,
    'SELECT email, senha, tipo FROM usuario WHERE id_usuario = ?'
);

mysqli_stmt_bind_param($consulta, 'i', $id);
mysqli_stmt_execute($consulta);

mysqli_stmt_bind_result(
    $consulta,
    $email,
    $senha,
    $tipo
);

if (mysqli_stmt_fetch($consulta)) {

    $usuario = [
        'email' => $email,
        'senha' => $senha,
        'tipo' => $tipo
    ];

}

mysqli_stmt_close($consulta);

if (!$usuario) {
    header('Location: tela-home-usuario.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $exclusao = mysqli_prepare(
        $conexao,
        'DELETE FROM usuario WHERE id_usuario = ?'
    );

    mysqli_stmt_bind_param($exclusao, 'i', $id);

    if (mysqli_stmt_execute($exclusao)) {

        mysqli_stmt_close($exclusao);

        header('Location: tela-home-usuario.php');
        exit;

    }

    $erro = 'Não foi possível excluir este usuário.';

    mysqli_stmt_close($exclusao);
}


function e($valor)
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Excluir usuário</title>

    <link
        rel="stylesheet"
        href="../../assets/style/style.css"
    >

</head>

<body>

<div class="estrutura-dashboard">

    <header class="barra-superior">

        <h1>
            PIA Enterprise
        </h1>

    </header>


    <aside class="menu-lateral">

        <nav>

            <a href="../../index.php">
                Dashboard
            </a>

            <a href="../sensor/tela-home-sensor.php">
                Sensores
            </a>

            <a href="../trem/lista_trem.php">
                Trens
            </a>

            <a href="../rotas/tela-home-rotas.php">
                Rotas
            </a>

            <a
                class="ativo"
                href="tela-home-usuario.php"
            >
                Usuários
            </a>

        </nav>

    </aside>


    <main class="conteudo conteudo-excluir-trem">

        <h2>
            Excluir usuário
        </h2>

        <p class="subtitulo-excluir-trem">
            Confirme a exclusão do usuário relacionado
        </p>


        <section class="painel-excluir-sensor">

            <div
                class="icone-excluir-sensor"
                aria-hidden="true"
            >
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
                    E-mail:
                    <?= e($usuario['email']) ?>
                </span>

                <span>
                    Senha:
                    <?= e($usuario['senha']) ?>
                </span>

                <span>
                    Tipo:
                    <?= e($usuario['tipo']) ?>
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
                    action="tela-exclusao-usuario.php"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?= e($id) ?>"
                    >

                    <button
                        class="botao-confirmar-exclusao"
                        type="submit"
                    >
                        Excluir usuário
                    </button>

                </form>


                <button
                    type="button"
                    onclick="window.location.href='tela-home-usuario.php'"
                >
                    Cancelar
                </button>

            </div>

        </section>

    </main>

</div>

</body>

</html>