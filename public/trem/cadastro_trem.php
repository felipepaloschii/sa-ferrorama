<!-- Identifica se não tem sessão ativa -->
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// conexão com o banco, confere se o usuário logado é ADMINISTRADOR. Se não for, bloqueia o acesso.

require_once '../../infra/verificar-admin.php';

// conexão com o banco, cria a variável $conexao, que é a ligação com o MySQL.

require_once '../../infra/conexao.php';

// Declaração de variáveis com valores vazios

$usuarios = []; //Guarda a lista de usuários, depois vai usar no "Usuário responsável".
$erro = ''; //Guarda a mensagem deerro, se tiver vazio, não tem erro.
$tipoErro = ''; //Guarda o tipo do erro (ajuda no CSS da mensagem).

// Busca os usuários no banco, para montar o select depois. A consulta vai trazer o id, email e tipo (adm/func), e ordena os emails por ordem alfabetica.

$resUsuarios = $conexao->query(
    "SELECT id_usuario, email, tipo FROM usuario ORDER BY email"
);

// Se a consulta deu certo ($resUsuarios não é false).

if ($resUsuarios) {
    // Transforma o resultado numa array PHP. 
    // MYSQLI_ASSOC faz as linhas virarem: ['id_usuario' => 1, 'email' => 'a@b.com', 'tipo' => 'administrador'].
    $usuarios = $resUsuarios->fetch_all(MYSQLI_ASSOC);
}

// Roda só quando o formulário foi ENVIADO (método POST)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Lê os dados enviados.
    // $_POST é o array com tudo que veio do formulário (atributo "name" de cada campo).
    // ?? signigica que se o campo não existir, retorna um texto vazio (deixa em branco).
    // -trim() remove os espaços sobrando no começo e no fim do texto.
    // (float) e (int) converte o texto para decimal/inteiro.

    $nome_trem     = trim($_POST['nome_trem'] ?? '');
    $modelo_trem   = trim($_POST['modelo_trem'] ?? '');
    $localizacao_i = trim($_POST['localizacao_i'] ?? '');
    $codigo        = trim($_POST['codigo'] ?? '');
    $capacidade    = (float) ($_POST['capacidade'] ?? 0);
    $ano           = (int) ($_POST['ano'] ?? 0);
    $status        = $_POST['status'] ?? '';

    // Se o campo veio preenchido (diferente de vazio), converte para inteiro.
    // Se veio vazio ("Sem usuário responsável"), guada null (NULL no banco).
    // Esse ? serve para valor_se_verdadeiro, valor_se_Falso.

    $id_usuario = ($_POST['id_usuario'] ?? '') !== ''
        ? (int) $_POST['id_usuario']
        : null;

    // Faz uma conferência se os dados fazem sentido antes de mexer com o banco
    // Se qualquer condição abaixo foi verdadeira ("||"" Significa "ou"), os dados são inválidos.
    // O servidor faz a validação dnv pois só com o required não é tão seguro sozinho.
    // "''"  Está em todas as variáveis significa vazias

    if (
        $nome_trem === '' ||
        $modelo_trem === '' ||
        $localizacao_i === '' ||
        $codigo === '' ||
        $capacidade <= 0 ||
        $ano <= 0 ||

        // "in_array" confere se o status está na lista dos status dados anteriormente.
        // o "true" no final ativa a comparação
        // o "!" inverte> "se o status NÃO está na lista permitida"

        !in_array($status, ['operacao', 'parado', 'manutencao', 'inativo'], true)
    ) {
        $erro = 'Preencha todos os campos obrigatórios corretamente.';
    } else {

        // Começa assumindo que existe o usuário "true". Aí só precisca checar no banco se o adm escolheu alguém,
        // Se deixou "Sem usuário", não precisa da verificação.

        $usuarioExiste = true;

        if ($id_usuario !== null) {

            // prepare(): monta uma consulta(query) com "?" no lugar do valor original.
            // É o Prepared Statement, serve para proteger contra o SQL Injection

            $check = $conexao->prepare(
                "SELECT 1 FROM usuario WHERE id_usuario = ?"
            );

            // bind_param() coloca o valor no lugar do "?".
            // "i" siginifca que o valor é inteiro.

            $check->bind_param("i", $id_usuario);

            // execute() roda a consulta

            $check->execute();

            // store_result() guarda o resultado na memoria do sistema para dar para contar quantas linhas voltam da consulta

            $check->store_result();

            // se voltou pelo menos 1 linha quer dizer que o usuário existe no banco

            $usuarioExiste = $check->num_rows > 0;

            // libera a consulta

            $check->close();
        }

        // se o usuário não existe, mostra erro, se existe, grava o trem.

        if (!$usuarioExiste) {
            $erro = 'Selecione um usuário válido da lista.';
        } else {

            // SQL de inserção. Cada "?" vai ser trocado por um valor, na mesma ordem das colunas listadas.

            $sql = "INSERT INTO trem
                    (nome_trem, modelo_trem, localizacao_i, codigo,
                     capacidade, ano, status, id_usuario)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            // Prepara o comando (se falhar, o statement fica false)

            $stmt = $conexao->prepare($sql);

            if ($stmt) {

                // bind_param() liga as variáveis aos 8 "?" do INSERT.

                $stmt->bind_param(

                    // a string "ssssdssi" diz o tipo de cada valor, em ordem (string, decimal, int)
                    // o ideal para o $ano seria int, mas funciona com string tambem

                    "ssssdssi",
                    $nome_trem,
                    $modelo_trem,
                    $localizacao_i,
                    $codigo,
                    $capacidade,
                    $ano,
                    $status,
                    $id_usuario
                );

                // try tenta execuar e se o banco devolver um erro, cai no "catch" avisando antes de quebrar a página

                try {
                    if ($stmt->execute()) {

                        // Sucesso, manda o usuario para a lista de trens

                        header("Location: lista_trem.php");

                        // exit interrompe o script, sem isso o PHP ia contunar executando o resto do código depois de redirecionar

                        exit;
                    }
                } catch (mysqli_sql_exception $e) {

                    // O código de erro 1062 quer dizer "entrada duplicada", a coluna "código" é UNIQUE(não pode se repetir) e já tem um trem com esse código

                    if ($e->getCode() === 1062) {
                        $erro = 'Esse código já está cadastrado em outro trem. Informe um código diferente.';

                        // Marca o tipo de erro pra usar um css específico no html

                        $tipoErro = 'duplicado';
                    } else {

                        // Qualquer outro erro no banco vai mandar essa mensagem genérica

                        $erro = 'Não foi possível cadastrar o trem.';
                    }
                }

                // libera os recursos do INSERT

                $stmt->close();
            } else {

                // Se o prepare() não funcionou (erro de sintaxe no SQL ou a tabela não existe), avisa o user

                $erro = "Erro ao preparar o cadastro.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Trem - PIA Enterprise</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body>

    <header class="barra-superior">
        <h1>PIA Enterprise</h1>

        <aside class="menu-lateral">
            <nav>
                <a href="../../index.php">Dashboard</a>
                <a href="../sensor/tela-home-sensor.php">Sensores</a>
                <a class="ativo" href="lista_trem.php">Trens</a>
                <a href="../rotas/tela-home-rotas.php">Rotas</a>

                <?php if ($_SESSION['tipo'] === 'administrador'): ?>
                    <a href="../usuario/tela-home-usuario.php">Usuários</a>
                <?php endif; ?>
            </nav>
        </aside>
    </header>

    <main class="area-principal cadastro-trem">

        <div class="cabecalho-cadastro-sensor">
            <div class="informacoes-pagina">
                <h2 class="titulo-pagina">Cadastrar novo trem</h2>
                <p class="descricao-pagina">
                    Preencha as informações para cadastrar um novo trem no sistema.
                </p>
            </div>

            <a href="lista_trem.php" class="botao-voltar-trem">
                Voltar para trens
            </a>
        </div>

        <?php if ($erro !== ''): ?>
            <div class="<?= $tipoErro === 'duplicado' ? 'erro-duplicado' : 'aviso-cadastro falha' ?>">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <section class="painel-cadastro-sensor">

            <form method="POST">

                <div class="grade-formulario-trem">

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="nome_trem">
                            Nome do trem<span class="obrigatorio">*</span>
                        </label>

                        <input
                            type="text"
                            id="nome_trem"
                            name="nome_trem"
                            placeholder="Ex.: Paloschi trem"
                            value="<?= htmlspecialchars($_POST['nome_trem'] ?? '') ?>"
                            required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="modelo_trem">
                            Modelo<span class="obrigatorio">*</span>
                        </label>

                        <input
                            type="text"
                            id="modelo_trem"
                            name="modelo_trem"
                            placeholder="Ex.: Velocidade Trem A"
                            value="<?= htmlspecialchars($_POST['modelo_trem'] ?? '') ?>"
                            required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="localizacao_i">
                            Localização Inicial<span class="obrigatorio">*</span>
                        </label>

                        <input
                            type="text"
                            id="localizacao_i"
                            name="localizacao_i"
                            placeholder="Ex.: Estação Central"
                            value="<?= htmlspecialchars($_POST['localizacao_i'] ?? '') ?>"
                            required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="codigo">
                            Código<span class="obrigatorio">*</span>
                        </label>

                        <input
                            type="text"
                            id="codigo"
                            name="codigo"
                            placeholder="Ex.: TREM-001"
                            value="<?= htmlspecialchars($_POST['codigo'] ?? '') ?>"
                            required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="id_usuario">
                            Usuário responsável
                        </label>

                        <select
                            id="id_usuario"
                            name="id_usuario"
                            class="select-usuario">

                            <option value="">Sem usuário responsável</option>

                            <?php foreach ($usuarios as $u): ?>
                                <option
                                    value="<?= (int) $u['id_usuario'] ?>"
                                    <?= (($_POST['id_usuario'] ?? '') == $u['id_usuario']) ? 'selected' : '' ?>>

                                    <?= htmlspecialchars($u['email']) ?>
                                    (<?= $u['tipo'] === 'administrador' ? 'Admin' : 'Funcionário' ?>)

                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="grupo-campo-trem">
                        <span class="rotulo-campo-trem">Status Inicial*</span>

                        <div class="opcoes-status-trem">

                            <label class="opcao-status-trem">
                                <input
                                    type="radio"
                                    name="status"
                                    value="operacao"
                                    <?= ($_POST['status'] ?? '') === 'operacao' ? 'checked' : '' ?>
                                    required>

                                <span class="marcador-status"></span>
                                Em operação
                            </label>

                            <label class="opcao-status-trem">
                                <input
                                    type="radio"
                                    name="status"
                                    value="parado"
                                    <?= ($_POST['status'] ?? '') === 'parado' ? 'checked' : '' ?>>

                                <span class="marcador-status"></span>
                                Parado
                            </label>

                            <label class="opcao-status-trem">
                                <input
                                    type="radio"
                                    name="status"
                                    value="manutencao"
                                    <?= ($_POST['status'] ?? '') === 'manutencao' ? 'checked' : '' ?>>

                                <span class="marcador-status"></span>
                                Manutenção
                            </label>

                            <label class="opcao-status-trem">
                                <input
                                    type="radio"
                                    name="status"
                                    value="inativo"
                                    <?= ($_POST['status'] ?? '') === 'inativo' ? 'checked' : '' ?>>

                                <span class="marcador-status"></span>
                                Inativo
                            </label>

                        </div>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="capacidade">
                            Capacidade<span class="obrigatorio">*</span>
                        </label>

                        <input
                            type="number"
                            id="capacidade"
                            name="capacidade"
                            step="any"
                            placeholder="Ex.: 300"
                            value="<?= htmlspecialchars($_POST['capacidade'] ?? '') ?>"
                            required>
                    </div>

                    <div class="grupo-campo-trem">
                        <label class="rotulo-campo-trem" for="ano">
                            Ano<span class="obrigatorio">*</span>
                        </label>

                        <input
                            type="number"
                            id="ano"
                            name="ano"
                            placeholder="Ex.: 2026"
                            value="<?= htmlspecialchars($_POST['ano'] ?? '') ?>"
                            required>
                    </div>

                </div>

                <div class="botoes-cadastro-trem">
                    <button type="submit" class="botao-salvar-trem">
                        Salvar trem
                    </button>

                    <a href="lista_trem.php" class="botao-voltar-trem">
                        Cancelar
                    </a>
                </div>

            </form>

        </section>
    </main>

</body>

</html>