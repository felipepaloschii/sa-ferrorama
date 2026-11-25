
<?php

// require_once inclui o arquivo de verificacao de administrador apenas uma vez
// esse arquivo verifica se existe uma sessao ativa e se o usuario possui permissao de administrador
require_once '../../infra/verificar-admin.php';

// inclui o arquivo de conexao com o banco de dados
// ../../ volta duas pastas pra localizar a pasta infra
require_once '../../infra/conexao.php';

// cria uma variavel com valor inicial zero pra contar quantos usuarios foram encontrados
$quantidade_usuario = 0;

// cria um array vazio pra armazenar os dados dos usuarios retornados pelo banco
$usuario = [];

// cria o comando SQL pra buscar os usuarios cadastrados
// SELECT define quais colunas serao consultadas na tabela
// FROM indica de qual tabela os dados serao buscados
// ORDER BY organiza os resultados pelo id_usuario
// DESC coloca os usuarios mais recentes primeiro
$sql = "SELECT id_usuario, email, tipo
        FROM usuario
        ORDER BY id_usuario DESC";

// query executa o comando SQL utilizando a conexao com o banco
// o resultado da consulta fica armazenado na variavel resultado
$resultado = $conexao->query($sql);

// verifica se a consulta foi executada e retornou um resultado valido
// if executa o bloco somente quando a condicao e verdadeira
if ($resultado) {

    // while repete o codigo enquanto existirem linhas pra buscar no resultado
    // fetch_assoc transforma cada linha em um array associativo
    // cada coluna pode ser acessada pelo nome, como id_usuario, email e tipo
    while ($linha = $resultado->fetch_assoc()) {

        // [] adiciona a linha atual no final do array usuario
        // assim todos os usuarios encontrados ficam armazenados pra serem exibidos no HTML
        $usuario[] = $linha;

        // ++ aumenta a quantidade de usuarios em 1 a cada registro encontrado
        $quantidade_usuario++;

    }

}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tela de Usuários</title>

    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body>

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

        <section class="cabecalho-sensor topo-lista-trens">

            <div>
                <h2 class="titulo-sensores">
                    Usuários
                </h2>

                <p class="descricao-sensores">
                    Gerencie os usuários cadastrados no sistema
                </p>
            </div>

            <a
                class="botao-novo-sensor botao-novo-trem"
                href="tela-cadastro-novousuario.php">

                + Novo Usuário

            </a>

        </section>

        <section class="conteudo-lista-sensor">

            <div class="topo-lista-sensores">

                <div class="cartao-sensores-ativos">

                    <div class="icone-usuario">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>

                    <div class="informacoes-sensor">

                        <h2>Usuários Cadastrados</h2>

                        <strong>
                            <?= $quantidade_usuario; ?>
                        </strong>

                    </div>

                </div>

            </div>

            <br>

            <section class="painel-lista-sensores">

                <div class="titulo-painel-lista">
                    <h2>Lista de Usuários</h2>
                </div>

                <div class="tabela-sensores">

                    <div class="cabecalho-tabela">

                        <span>ID</span>
                        <span>E-mail</span>
                        <span>Tipo</span>
                        <span>Ações</span>

                    </div>

                    <?php if (count($usuario) > 0): ?>

                        <?php foreach ($usuario as $linhaUsuario): ?>

                            <div class="linha-tabela">

                                <span>
                                    <?= htmlspecialchars($linhaUsuario['id_usuario']); ?>
                                </span>

                                <span>
                                    <?= htmlspecialchars($linhaUsuario['email']); ?>
                                </span>

                                <span>
                                    <?= htmlspecialchars($linhaUsuario['tipo']); ?>
                                </span>

                                <span class="acoes">

                                    <a
                                        href="tela-edit-usuario.php?id=<?= (int) $linhaUsuario['id_usuario']; ?>"
                                        class="botao-editar"
                                        title="Editar">

                                        E

                                    </a>

                                    <a
                                        href="tela-excluir-usuario.php?id=<?= (int) $linhaUsuario['id_usuario']; ?>"
                                        class="botao-excluir"
                                        title="Excluir">

                                        D

                                    </a>

                                </span>

                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

            </section>

        </section>

    </main>

</body>

</html>
