<?php

include '../../infra/conexao.php';

$quantidade_usuario = 0;
$usuarios = [];

$sql = "SELECT id_usuario, email, senha, tipo FROM usuario ORDER BY id_usuario DESC";

$resultado = $conexao->query($sql);

if ($resultado) {

while ($usuarios = $resultado->fetch_assoc()) {
    $usuario[] = $usuarios;
    $quantidade_usuario++;
}

}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de Usuários</title>
    <link rel="stylesheet" href="../../assets/style/style.css">

</head>
<body>

    <header class="barra-superior">
        <h1>PIA Enterprise</h1>

    </header>

    <aside class="menu-lateral">
        <nav>
            <a href="../../index.php">Dashboard</a>
            <a href="tela-home-sensor.php">Sensores</a>
            <a href="../trem/cadastro_trem.php">Trens</a>
            <a href="../rotas/tela-home-rotas.php">Rotas</a>
            <a class="ativo" href="tela-home-usuario.php">Usuários</a>
        </nav>
    </aside>

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

        <section class= "conteudo-lista-sensor">
        <div class="topo-lista-sensores">
            <div class="cartao-sensores-ativos">
                <div class ="icone-usuario">

                        <span></span>
                        <span></span>
                        <span></span>

                </div>

                 <div class="informacoes-sensor">

                        <h2>Usuários Cadastrados</h2>

                        <strong>
                            <?php echo $quantidade_usuario; ?>
                        </strong>
                    </div>
                </div>

               
            </div>

             

        <section class="painel-lista-sensores">
            <div class="titulo-painel-lista">
                <h2>Lista de Usuários</h2>
            </div>
            
            <div class="tabela-sensores">

            <div class="cabecalho-tabela">

             <span>ID</span>
                        <span>E-mail</span>
                        <span>Senha</span>
                        <span>Tipo</span>
                        <span>Ações</span>
            </div>
            <?php if (count($usuario) > 0) { ?>

    <?php foreach ($usuario as $usuario) { ?>

        <div class="linha-tabela">

            <span>
                <?php echo htmlspecialchars($usuario['id_usuario']); ?>
            </span>

            <span>
                <?php echo htmlspecialchars($usuario['email']); ?>
            </span>

            <span>
                <?php echo htmlspecialchars($usuario['senha']); ?>
            </span>

            <span>
               <?php echo htmlspecialchars($usuario['tipo']); ?>
            </span>

            <span class="acoes">

                <a href="tela-edição-rotas.php?id=<?php echo $usuario['id_usuario']; ?>"
                   class="botao-editar"
                   title="Editar">✎</a>

                <a href="tela-exclusão-rotas.php?id=<?php echo $usuario['id_usuario']; ?>"
                   class="botao-excluir"
                   title="Excluir">🗑</a>

            </span>

        </div>

    <?php } ?>

<?php } ?>

            </div>
        </section>
    </main>
</body>
</html>
