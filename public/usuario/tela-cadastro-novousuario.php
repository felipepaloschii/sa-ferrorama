<?php

include '../../infra/conexao.php';

$mensagem = '';
$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];

    if (empty($nome) || empty($email) || empty($senha) || empty($tipo)) {

        $erro = 'Preencha todos os campos.';

    } elseif (strlen($senha) < 8) {

        $erro = 'A senha deve ter no mínimo 8 caracteres.';

    } else {

        $verificar = "SELECT id_usuario FROM usuario WHERE email = ?";

        $stmt = $conexao->prepare($verificar);

        if ($stmt) {

            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {

                $erro = 'Este e-mail já está cadastrado.';

            } else {

                $sql = "INSERT INTO usuario (nome, email, senha, tipo) VALUES (?, ?, ?, ?)";

                $stmt2 = $conexao->prepare($sql);

                if ($stmt2) {

                    $stmt2->bind_param(
                        "ssss",
                        $nome,
                        $email,
                        $senha,
                        $tipo
                    );

                    if ($stmt2->execute()) {

                        $mensagem = 'Novo usuário cadastrado com sucesso!';

                    } else {

                        $erro = 'Não foi possível cadastrar o usuário.';

                    }

                    $stmt2->close();

                } else {

                    $erro = 'Erro ao preparar o cadastro do usuário.';

                }
            }

            $stmt->close();

        } else {

            $erro = 'Erro ao verificar o e-mail.';

        }
    }
}

?>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Usuário</title>

    <link rel="stylesheet" href="../../assets/style/style.css">

</head>

<body>

    <header class="barra-superior">

        <h1>PIA Enterprise</h1>

    </header>


    <aside class="menu-lateral">

        <nav>

            <a href="../../index.php">Dashboard</a>

            <a href="../sensor/tela-home-sensor.php">Sensores</a>

            <a href="../../public/trem/cadastro_trem.php">Trens</a>

            <a href="../../public/rotas/tela-home-rotas.php">Rotas</a>

            <a class="ativo" href="tela-home-usuario.php">Usuários</a>

        </nav>

    </aside>


    <main class="area-principal">

        <section class="cabecalho-cadastro-sensor">

            <div class="informacoes-pagina">

                <h1 class="titulo-pagina">
                    Cadastrar novo usuário
                </h1>

                <p class="descricao-pagina">
                    Preencha as informações para cadastrar um novo usuário.
                </p>

            </div>


            <button
                class="botao-voltar-sensores"
                onclick="window.location.href='tela-home-usuario.php'">

                🠐 Voltar para usuários

            </button>

        </section>


        <section class="painel-cadastro-sensor">

            <?php if ($mensagem != '') { ?>

                <p style="color: green; font-weight: bold;">
                    <?php echo $mensagem; ?>
                </p>

            <?php } ?>


            <?php if ($erro != '') { ?>

                <p style="color: red; font-weight: bold;">
                    <?php echo $erro; ?>
                </p>

            <?php } ?>


            <form
                class="formulario-cadastro-sensor"
                method="POST">


                <div class="linha-campos-formulario">

                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            Nome completo
                        </label>

                        <input
                            type="text"
                            name="nome"
                            class="input-nome_sensorsensor">

                    </div>


                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            E-mail
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="input-nome_sensorsensor">

                    </div>

                </div>


                <div class="linha-campos-formulario">

                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            Senha
                        </label>

                        <input
                            type="password"
                            name="senha"
                            class="input-nome_sensorsensor">

                    </div>


                    <div class="grupo-nome_sensor-sensor">

                        <label class="nome_sensor-sensor">
                            Tipo de usuário
                        </label>

                        <select
                            name="tipo"
                            class="select-tipo-sensor">

                            <option value="">Selecione</option>

                            <option value="admin">
                                Administrador
                            </option>

                            <option value="usuario">
                                Usuário
                            </option>

                        </select>

                    </div>

                </div>


                <div class="linha-status-e-descricao">

                    <div class="grupo-descricao-sensor">

                        <label class="descricao-sensor">
                            Informações
                        </label>

                        <textarea
                            class="textarea-descricao-sensor"
                            readonly>O usuário cadastrado poderá acessar o sistema de acordo com o tipo selecionado.</textarea>

                    </div>

                </div>


                <div class="area-botoes-formulario">

                    <button
                        type="submit"
                        class="botao-salvar-sensor">

                        Salvar usuário

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