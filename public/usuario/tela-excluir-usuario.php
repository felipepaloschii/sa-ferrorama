

<
<?php

// verifica se o formulario foi enviado pelo metodo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // verifica se o administrador esta tentando excluir a propria conta
    if ($id == $_SESSION['id_usuario']) {

        // impede a exclusao da propria conta
        $erro = 'Você não pode excluir sua própria conta.';

    } else {

        // prepara o comando SQL pra excluir o usuario pelo id
        $exclusao = mysqli_prepare(
            $conexao,
            'DELETE FROM usuario WHERE id_usuario = ?'
        );

        // verifica se a consulta foi preparada corretamente
        if ($exclusao) {

            // associa o id ao marcador da consulta
            // i indica que o valor e um numero inteiro
            mysqli_stmt_bind_param(
                $exclusao,
                'i',
                $id
            );

            // executa a exclusao no banco de dados
            if (mysqli_stmt_execute($exclusao)) {

                // verifica se algum registro foi excluido
                if (mysqli_stmt_affected_rows($exclusao) > 0) {

                    // encerra a consulta
                    mysqli_stmt_close($exclusao);

                    // redireciona pra listagem depois da exclusao
                    header('Location: tela-home-usuario.php');
                    exit;
                }
            }

            // encerra a consulta e libera os recursos
            mysqli_stmt_close($exclusao);

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
