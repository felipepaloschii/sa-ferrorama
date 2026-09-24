<?php

include'../../infra/conexao.php';

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_trem = $_POST['id_trem'];
    $nome_rota = $_POST['nome_rota'];
    $codigo = $_POST['codigo'];
    $distancia = $_POST['distancia'];
    $tempo_estimado = $_POST['tempo_estimado'];
    $capacidade_rota = $_POST['capacidade_rota'];

    $sql = "INSERT INTO rotas (id_trem, nome_rota, codigo, distancia, tempo_estimado, capacidade_rota) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("isssdd", $id_trem, $nome_rota, $codigo, $distancia, $tempo_estimado, $capacidade_rota);

    if ($stmt->execute() === TRUE) {

        header("Location: tela-home-rotas.php");
        exit;

    } else {

        $mensagem = "Erro ao cadastrar rota.";

    }
}

?><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar rota</title>
    
    <link
        rel="stylesheet"
        href="../../assets/style/style.css"
    >
</head>

<body id="pagina-rota">

    <header class="barra-superior">

        <h1>PIA Enterprise</h1>

        <div class="acoes-superiores">

            <div class="aba-usuario">
                <a href="../usuario/tela-cadastro-user.php">
                    Meu perfil
                </a>

                <a href="../logout/logout.php">
                    Sair
                </a>

            </div>

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

            <a
                class="ativo"
                href="tela-home-rotas.php"
            >
                Rotas
            </a>

        </nav>

    </aside>

    <main class="conteudo">

        <section class="area-cadastro-rota">

            <h2>Cadastrar rotas</h2>

            <p class="subtitulo-rota">
                Preencha as informações para cadastrar a rota.
            </p>


            <?php if ($mensagem != ''): ?>

                <div class="mensagem-sucesso">
                    <?= htmlspecialchars($mensagem) ?>
                </div>

            <?php endif; ?>


            <?php if ($erro != ''): ?>

                <div class="mensagem-erro">
                    <?= htmlspecialchars($erro) ?>
                </div>

            <?php endif; ?>


            <form
                class="formulario-rota"
                method="POST"
                action=""
            >

                <div class="formulario-grid">

                    <div class="campo-rota">

                        <label for="nome_rota">
                            Nome da rota
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="nome_rota"
                            name="nome_rota"
                            placeholder="Ex.: Rota Joinville → Barra Velha"
                            required
                        >

                    </div>

                    <div class="campo-rota">

                        <label for="trem">
                            Trem
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="trem"
                            name="trem"
                            placeholder="Ex.: Trem 1947"
                            required
                        >

                    </div>

                    <div class="campo-rota">

                        <label for="tempo_estimado">
                            Tempo Estimado
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="tempo_estimado"
                            name="tempo_estimado"
                            placeholder="Ex.: 00h45m"
                            required
                        >

                    </div>

                    <div class="campo-rota">

                        <label for="codigo">
                            Código
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="codigo"
                            name="codigo"
                            placeholder="Ex.: RT-016"
                            required
                        >

                    </div>

                    <div class="campo-rota campo-status">

                        <label>
                            Status Inicial
                            <span>*</span>
                        </label>


                        <div class="radio-rota">

                            <input
                                type="radio"
                                id="ativa"
                                name="status"
                                value="Ativa"
                                checked
                            >

                            <label for="ativa">
                                Ativa
                            </label>

                        </div>


                        <div class="radio-rota">

                            <input
                                type="radio"
                                id="inativa"
                                name="status"
                                value="Inativa"
                            >

                            <label for="inativa">
                                Inativa
                            </label>

                        </div>

                    </div>
                    
                    <div class="campo-rota">

                        <label for="capacidade">
                            Capacidade
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="capacidade"
                            name="capacidade"
                            placeholder="Ex.: 100 Ton"
                            required
                        >
                        </div>

                        <div class="campo-rota">

                        <label for="distancia">
                            Distância
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="distancia"
                            name="distancia"
                            placeholder="Ex.: 50km"
                            required
                        >

                    </div>

                </div>

                <div class="botoes-rota">

                    <button
                        type="submit"
                        class="botao-salvar-rota"
                    >
                        Salvar Rota
                    </button>


                    <a
                        href="tela-home-rotas.php"
                        class="botao-voltar-rota"
                    >
                        Voltar para Rotas
                    </a>

                </div>

            </form>

        </section>

    </main>

</body>

</html>