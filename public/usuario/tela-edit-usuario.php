<?php

include '../../infra/conexao.php';

$mensagem = '';
$erro = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: tela-home-usuario.php');
    exit;
}

$id = (int) $_GET['id'];

$sql = "SELECT * FROM usuario WHERE id_usuario = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header('Location: tela-home-usuario.php');
    exit;
}

$usuario = $resultado->fetch_assoc();

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];

    $sql = "UPDATE usuario SET
            email = ?,
            senha = ?,
            tipo = ?
            WHERE id_usuario = ?";

    $stmt = $conexao->prepare($sql);

    if ($stmt) {

        $stmt->bind_param(
            "sssi",
            $email,
            $senha,
            $tipo,
            $id
        );

        if ($stmt->execute()) {

            $mensagem = 'Usuário atualizado com sucesso!';

            $usuario['email'] = $email;
            $usuario['senha'] = $senha;
            $usuario['tipo'] = $tipo;

        } else {

            $erro = 'Não foi possível atualizar o usuário.';

        }

        $stmt->close();

    } else {

        $erro = 'Erro ao preparar a atualização do usuário.';

    }
}

?>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Usuário</title>

    <link rel="stylesheet" href="../../assets/style/style.css">

</head>

<body>

    <header class="barra-superior">

        <h1>PIA Enterprise</h1>

        <div class="acoes-superiores">

            <span class="ponto-online"></span>

            <span class="texto-online">
                Online
            </span>

            <span class="icone-sino"></span>

            <span class="icone-usuario"></span>

        </div>

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

            <a class="ativo" href="tela-home-usuario.php">
                Usuários
            </a>

        </nav>

    </aside>

    <main class="area-principal">

        <section class="cabecalho-cadastro-sensor">

            <div class="informacoes-pagina">

                <h1 class="titulo-pagina">
                    Editar usuário
                </h1>

                <p class="descricao-pagina">
                    Preencha as informações para editar um usuário existente no sistema.
                </p>

            </div>

            <button
                class="botao-voltar-sensores"
                onclick="window.location.href='tela-home-usuario.php'">

                ← Voltar para usuários

            </button>

        </section>

        <?php if ($mensagem != ''): ?>

            <p>
                <?php echo $mensagem; ?>
            </p>

        <?php endif; ?>

        <?php if ($erro != ''): ?>

            <p>
                <?php echo $erro; ?>
            </p>

        <?php endif; ?>

        <section class="painel-cadastro-sensor">

            <form
                class="formulario-cadastro-sensor"
                method="POST"
                action="">

                <div class="linha-campos-formulario">

                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            E-mail *
                        </label>

                    </div>

                    <input
                        type="email"
                        name="email"
                        class="input-nome_sensorsensor"
                        value="<?php echo htmlspecialchars($usuario['email']); ?>"
                        required>


                    <div class="grupo-tipo-sensor">

                        <label class="tipo-sensor">
                            Senha *
                        </label>

                        <input
                            type="text"
                            name="senha"
                            class="select-tipo-sensor"
                            value="<?php echo htmlspecialchars($usuario['senha']); ?>"
                            required>

                    </div>

                </div>


                <div class="linha-campos-formulario">

                    <div class="grupo-tipo-sensor">

                        <label class="tipo-sensor">
                            Tipo de usuário *
                        </label>

                        <select
                            name="tipo"
                            class="select-tipo-sensor"
                            required>

                            <option value="">
                                Selecione o tipo
                            </option>

                            <option
                                value="admin"
                                <?php
                                if ($usuario['tipo'] == 'admin') {
                                    echo 'selected';
                                }
                                ?>>
                                Administrador
                            </option>

                            <option
                                value="usuario"
                                <?php
                                if ($usuario['tipo'] == 'usuario') {
                                    echo 'selected';
                                }
                                ?>>
                                Usuário
                            </option>

                        </select>

                    </div>

                </div>


                <div class="area-botoes-formulario">

                    <button
                        type="submit"
                        class="botao-salvar-sensor">

                        Salvar alterações

                    </button>

                    <button
                        type="button"
                        class="botao-cancelar-cadastro"
                        onclick="window.location.href='tela-home-usuario.php'">

                        Cancelar

                    </button>

                </div>

            </form>

        </section>

    </main>

</body>

</html>