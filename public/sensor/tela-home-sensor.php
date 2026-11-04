<?php 


if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (
    !isset($_SESSION['id_usuario']) ||
    !isset($_SESSION['tipo'])
) {
    header('Location: ../usuario/tela-login.php');
    exit;
}

require_once '../../infra/conexao.php';


$quantidade_ativos = 0;
$sensores = [];

$sql = "SELECT * FROM sensor ORDER BY id DESC";

$resultado = $conexao->query($sql);

if ($resultado) {
    while ($sensor = $resultado->fetch_assoc()) {
        $sensores[] = $sensor;

        if (strtolower(trim($sensor['status_inicial'])) == 'ativo') {
            $quantidade_ativos++;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tela de Sensores</title>

    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body>

<header class="barra-superior">
    <h1>PIA Enterprise</h1>

    <aside class="menu-lateral">
        <nav>
            <a href="../../index.php">Dashboard</a>
            <a class="ativo" href="tela-home-sensor.php">Sensores</a>
            <a href="../trem/lista_trem.php">Trens</a>
            <a href="../rotas/tela-home-rotas.php">Rotas</a>

            <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
                <a href="../usuario/tela-home-usuario.php">Usuários</a>
            <?php endif; ?>
        </nav>
    </aside>
</header>

<main class="area-principal">

    <section class="cabecalho-sensor">

        <div>
            <h1 class="titulo-sensores">
                Listagem de Sensores
            </h1>

            <p class="descricao-sensores">
               
            </p>
        </div>

         <div class="topo-lista-sensores">
            

            <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
            <a class="botao-novo-sensor botao-novo-trem" href="tela-cadastro-sensor.php">+ Novo Sensor</a>
            <?php endif; ?>
         

        </div>

    </section>




        <section class="painel-lista-sensores">


            <div class="tabela-sensores">

                <div class="cabecalho-tabela">

                    <span>ID</span>
                    <span>Nome</span>
                    <span>Tipo</span>
                    <span>Localização</span>
                    <span>Unidade</span>
                    <span>Limite</span>
                    <span>Status</span>
                    <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
                        <span>Ações</span>
                    <?php endif; ?>
                 
                </div>


                <?php if (count($sensores) > 0): ?>

                    <?php foreach ($sensores as $sensor): ?>

                        <div class="linha-tabela">

                            <span>
                                <?php echo htmlspecialchars($sensor['id']); ?>
                            </span>

                            <span>
                                <?php echo htmlspecialchars($sensor['nome_sensor']); ?>
                            </span>

                            <span>
                                <?php echo htmlspecialchars($sensor['tipo_sensor']); ?>
                            </span>

                            <span>
                                <?php echo htmlspecialchars($sensor['localizacao']); ?>
                            </span>

                            <span>
                                <?php echo htmlspecialchars($sensor['unidade_medida']); ?>
                            </span>

                            <span>
                                <?php echo htmlspecialchars($sensor['limite_alerta']); ?>
                            </span>

                            <span>

                                <?php if (strtolower(trim($sensor['status_inicial'])) == 'ativo'): ?>

                                    <span class="status ativo">
                                        Ativo
                                    </span>

                                <?php else: ?>

                                    <span class="status inativo">
                                        Inativo
                                    </span>

                                <?php endif; ?>

                            </span>

                            <?php if ($_SESSION['tipo'] === 'administrador'): ?>
                            <span class="acoes">

                                <a
                                    href="tela-edit-sensor.php?id=<?php echo $sensor['id']; ?>"
                                    class="botao-editar"
                                    title="Editar"
                                >
                                  E  
                                </a>

                                <a
                                    href="tela-excluir-sensor.php?id=<?php echo $sensor['id']; ?>"
                                    class="botao-excluir"
                                    title="Excluir"
                                >
                                   D 
                                </a>

                            </span>
                            <?php endif; ?>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="nenhum-sensor">
                        Nenhum sensor cadastrado.
                    </div>

                <?php endif; ?>

            </div>

        </section>

    </section>

</main>

</body>
</html> 