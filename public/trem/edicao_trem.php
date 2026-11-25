<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once '../../infra/verificar-admin.php';
require_once '../../infra/conexao.php';

// input_input() Lé um valor de fora (URL, form) e valida.
// INPUT_GET pega da URL (?id=5)
// FILTER_VALIDADE_INT só aceita se for um número inteiro
// se o valor for invalido (por exepmlo: ?id=abc), o resultado é false ou null


$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// variáveis para mensaem de erro, se estão vazias é porque não tem erro por enquanto

$erro = '';
$tipoErro = '';

// Recupera o ID enviado pelo formulário
// Quando o formulário é enviado com o método POST, o id não vem pela URL, vem por um campo escondido (<input type="hidden" name="id">) lá no HTML,
// Então se for por POST, o id lido é o INPUT_POST e sobrescreve o valor

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

// Se o id não existe, é inválido ou é menor que 1, não tem o que editar, 
// então redireiona para a lista de trens e para o script com o "exit".

if (!$id || $id < 1) {
    header('Location: lista_trem.php');
    exit;
}

// // Busca os usuários disponíveis
// O [] começa vazio, e é preenchido se a consulta funcionar

$usuarios = [];

// Consulta simples(sem os valores vindos do usuário), então não precisa de prepare. 
// Traz id, e-mail e tipo, ordenados pelo email assim como no cadastro.

$resultadoUsuarios = $conexao->query(
    "SELECT id_usuario, email, tipo FROM usuario ORDER BY email"
);

// Se a consulta deu certo, transofrma o resultado em um array PHP.
// MYSQLI_ASSOC faz cada linha virar algo tipo:
//  ['id_usuario' => 'email' => 'teste@.com' => 'administrador']

if ($resultadoUsuarios) {
    $usuarios = $resultadoUsuarios->fetch_all(MYSQLI_ASSOC);
}

// // Busca os dados do trem
// Usa prepared statement, explicado na tela de cadastro

$consulta = $conexao->prepare(
    "SELECT nome_trem, modelo_trem, localizacao_i, codigo,
            capacidade, ano, status, id_usuario
     FROM trem
     WHERE id = ?"
);

// Liga o valor de $id ao "?", o "i" significa que é um número inteiro

$consulta->bind_param("i", $id);

// Faz a consulta(query) no banco

$consulta->execute();

// Pega o resultadoe transofrma a primeira liunha em um array associativo.
// Exemplo: $trem['nome_trem'], $trem['codigo'] etc.
// Se não tiver um tem com esse id, o $trem fica como null/false

$resultado = $consulta->get_result();
$trem = $resultado->fetch_assoc();

// Libera as funções da consulta

$consulta->close();

// Se não achou o trem ou seja o id não existe no banco, volta para a lista

if (!$trem) {
    header('Location: lista_trem.php');
    exit;
}

// Atualiza os dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // trim() remove os espaços sobrando no começo e fim do texto.
    // "?? ''" singinfica que o campo não existir, use um texto vazio.

    $trem['nome_trem'] = trim($_POST['nome_trem'] ?? '');
    $trem['modelo_trem'] = trim($_POST['modelo_trem'] ?? '');
    $trem['localizacao_i'] = trim($_POST['localizacao_i'] ?? '');
    $trem['codigo'] = trim($_POST['codigo'] ?? '');
    $trem['capacidade'] = trim($_POST['capacidade'] ?? '');
    $trem['ano'] = trim($_POST['ano'] ?? '');
    $trem['status'] = $_POST['status'] ?? '';

    // No caso de usuário resonsável, é OPCIONAL.
    // -Campo vazio -> o trem fica sem responsável (null = NULL no banco).
    // - Campo cheio -> Converte para inteiro, se não foi número válido, 
    // fillter_var devolve false
    // Permite deixar o trem sem usuário associado

    $usuarioSelecionado = trim($_POST['id_usuario'] ?? '');

    if ($usuarioSelecionado === '') {
        $trem['id_usuario'] = null;
    } else {
        $trem['id_usuario'] = filter_var(
            $usuarioSelecionado,
            FILTER_VALIDATE_INT
        );
    }

    // Lista dos únicos valores aceitos para o status do trem.


    $statusValidos = ['operacao', 'parado', 'manutencao', 'inativo'];

    // Aqui é a VALIDAÇÃO DOS CAMPOS
    // Se qualquer condição foi verdadeira, os dados são inválidos. 
    // O navegador já usa required para validar, mas isso não é seguro sozinho, pois tem como burlar o HTML
    // Então o servidor valida novamente

    if (
        $trem['nome_trem'] === '' ||
        $trem['modelo_trem'] === '' ||
        $trem['localizacao_i'] === '' ||
        $trem['codigo'] === '' ||
        $trem['capacidade'] === '' ||
        $trem['ano'] === '' ||
        !is_numeric($trem['capacidade']) ||

        // preg_match com /^\d{4}$/ confere se o ano tem EXATAMENTE 4 dígitos.
        // Por exemplo, se eu colocar "2025" passa, mas "25" ou "dois mil e vinte", não passa.

        !preg_match('/^\d{4}$/', $trem['ano']) ||

        // Status precisa estar na lista de permitidos
        // (o "true" ativa a comparação estrita, que compara também o tipo do valor).

        !in_array($trem['status'], $statusValidos, true) ||

        // Se o usuário escolheu alguém, mas o valor não é um número válido.

        ($usuarioSelecionado !== '' && $trem['id_usuario'] === false)
    ) {
        $erro = 'Preencha todos os campos com valores válidos.';
        $tipoErro = 'normal';
    } else {

        // Serve para CONFERIR se o usuário escolhido existe no banco.
        // Começa assumindo que existe(true), só consulta no banco só adm escolher algum usuario

        $usuarioExiste = true;

        // Confere o usuário somente se algum foi selecionado

        if ($trem['id_usuario'] !== null) {
            $verifica = $conexao->prepare(
                "SELECT id_usuario FROM usuario WHERE id_usuario = ?"
            );

            $verifica->bind_param("i", $trem['id_usuario']);
            $verifica->execute();

            // store_result() guarda o resultado na memória para contar as linhas que retornam

            $verifica->store_result();


            // se voltou 1 linha pelo menos, o user existe.

            $usuarioExiste = $verifica->num_rows > 0;
            $verifica->close();
        }

        if (!$usuarioExiste) {
            $erro = 'Selecione um usuário responsável válido.';
            $tipoErro = 'normal';
        } else {

            // Atualizar o trem no banco(UPDATE)
            // Cada "?" vai ser trocado por um valor, na mesma ordem.
            // O "WHERE id = ?" é ESSENCIAL: garante que só o trem escolhido seja alterado.
            // Sem ele, todos os trens seriam modificados.

            $sql = "UPDATE trem SET
                        nome_trem = ?,
                        modelo_trem = ?,
                        localizacao_i = ?,
                        codigo = ?,
                        capacidade = ?,
                        ano = ?,
                        status = ?,
                        id_usuario = ?
                    WHERE id = ?";

            $atualizacao = $conexao->prepare($sql);

            // bind_param(): liga as variáveis aos 9 "?" do UPDATE.
            // A string "ssssdssii" informa o tipo de cada valor, em ordem, por exemplo "int, string, decimal".

            $atualizacao->bind_param(
                "ssssdssii",
                $trem['nome_trem'],
                $trem['modelo_trem'],
                $trem['localizacao_i'],
                $trem['codigo'],
                $trem['capacidade'],
                $trem['ano'],
                $trem['status'],
                $trem['id_usuario'],
                $id
            );

            // try tenta execuar e se o banco devolver um erro, cai no "catch" avisando antes de quebrar a página


            try {

                // Sucesso, manda o usuario para a lista de trens


                if ($atualizacao->execute()) {

                    // Sucesso, manda o usuario para a lista de trens

                    $atualizacao->close();
                    header('Location: lista_trem.php');

                    // exit: interrompe o script para nada mais rodar depois do redirecionamento.

                    exit;
                }

                // Se execute() devolveu false, mostra uma mensagem genérica.

                $erro = 'Não foi possível salvar as alterações. Tente novamente.';
                $tipoErro = 'normal';
            } catch (mysqli_sql_exception $e) {

                // O código de erro 1062 quer dizer "entrada duplicada", a coluna "código" é UNIQUE(não pode se repetir) e já tem um trem com esse código

                if ($e->getCode() === 1062) {
                    $erro = 'Esse código já está cadastrado em outro trem. Informe um código diferente.';

                    // Marca o tipo para usar um estilo visual específico.

                    $tipoErro = 'duplicado';
                } else {

                    // Qualquer outro erro do banco recebe mensagem genérica.


                    $erro = 'Não foi possível salvar as alterações. Tente novamente.';
                    $tipoErro = 'normal';
                }
            }

             // Libera os recursos do comando UPDATE (só chega aqui se NÃO houve sucesso, pois no sucesso o script já terminou no exit).

            $atualizacao->close();
        }
    }
}

// Essa função serve para neutralizar caracteres especiais de um texto (como <> " ') antes de mostrar no HTML, protege contra XSS,
// um ataque que aluém grava o código malicioso em um campo para que ele rode no navegador de outras pessoas.
// - (string) $valor: converte qualquer valor (número, null) em texto.
//     - ENT_QUOTES: também converte aspas simples e duplas.
//     - 'UTF-8': codificação correta para acentos.
// Mesmo estando declarada aqui embaixo, ela pode ser usada no HTML porque
// o PHP lê funções declaradas no nível principal do arquivo antes de rodar.

function e($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar trem</title>
    <link rel="stylesheet" href="../../assets/style/style.css">
</head>

<body>
    <div class="estrutura-dashboard">

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

        <main class="conteudo conteudo-edicao-trem">

            <div class="informacoes-pagina">
                <h1 class="titulo-pagina">Editar trem</h1>

                <p class="descricao-pagina">
                    Altere as informações do trem cadastrado.
                </p>
            </div>

            <br>
            <section class="painel-formulario-trem">

                <?php if ($erro !== ''): ?>
                    <p class="<?= $tipoErro === 'duplicado' ? 'erro-duplicado' : 'aviso-edicao-trem' ?>">
                        <?= e($erro) ?>
                    </p>
                <?php endif; ?>

                <form method="post" action="edicao_trem.php?id=<?= e($id) ?>">

                    <input type="hidden" name="id" value="<?= e($id) ?>">

                    <div class="grade-formulario-trem">

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="nome_trem">
                                Nome do trem<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="text"
                                id="nome_trem" name="nome_trem" maxlength="100"
                                value="<?= e($trem['nome_trem']) ?>" required>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="modelo_trem">
                                Modelo<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="text"
                                id="modelo_trem" name="modelo_trem" maxlength="100"
                                value="<?= e($trem['modelo_trem']) ?>" required>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="localizacao_i">
                                Localização inicial<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="text"
                                id="localizacao_i" name="localizacao_i" maxlength="150"
                                value="<?= e($trem['localizacao_i']) ?>" required>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="codigo">
                                Código<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="text"
                                id="codigo" name="codigo" maxlength="30"
                                value="<?= e($trem['codigo']) ?>" required>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="id_usuario">
                                Usuário responsável
                            </label>

                            <select id="id_usuario" name="id_usuario"
                                class="select-usuario">

                                <option value=""
                                    <?= $trem['id_usuario'] === null ? 'selected' : '' ?>>
                                    Nenhum usuário
                                </option>

                                <?php foreach ($usuarios as $u): ?>
                                    <option value="<?= (int) $u['id_usuario'] ?>"
                                        <?= ((int) $trem['id_usuario'] === (int) $u['id_usuario']) ? 'selected' : '' ?>>
                                        <?= e($u['email']) ?>
                                        (<?= $u['tipo'] === 'administrador' ? 'Admin' : 'Funcionário' ?>)
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="grupo-campo-trem opcoes-status-trem">
                            <span class="rotulo-campo-trem">
                                Status inicial<span class="obrigatorio">*</span>
                            </span>

                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="operacao"
                                    <?= $trem['status'] === 'operacao' ? 'checked' : '' ?> required>
                                <span class="marcador-status"></span>Em operação
                            </label>

                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="parado"
                                    <?= $trem['status'] === 'parado' ? 'checked' : '' ?>>
                                <span class="marcador-status"></span>Parado
                            </label>

                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="manutencao"
                                    <?= $trem['status'] === 'manutencao' ? 'checked' : '' ?>>
                                <span class="marcador-status"></span>Manutenção
                            </label>

                            <label class="opcao-status-trem">
                                <input type="radio" name="status" value="inativo"
                                    <?= $trem['status'] === 'inativo' ? 'checked' : '' ?>>
                                <span class="marcador-status"></span>Inativo
                            </label>
                        </div>

                        <div class="grupo-campo-trem">
                            <label class="rotulo-campo-trem" for="capacidade">
                                Capacidade<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="number"
                                id="capacidade" name="capacidade" min="0" step="0.01"
                                value="<?= e($trem['capacidade']) ?>" required>
                        </div>

                        <div class="grupo-campo-trem campo-ano-trem">
                            <label class="rotulo-campo-trem" for="ano">
                                Ano<span class="obrigatorio">*</span>
                            </label>

                            <input class="campo-edicao-trem" type="text"
                                id="ano" name="ano" maxlength="4" pattern="[0-9]{4}"
                                value="<?= e($trem['ano']) ?>" required>
                        </div>

                    </div>

                    <div class="acoes-edicao-trem">

                        <button class="botao-salvar-sensor" type="submit">
                            Salvar alterações
                        </button>

                        <a class="botao-cancelar-cadastro" href="lista_trem.php">
                            Voltar para trens
                        </a>

                    </div>

                </form>
            </section>
        </main>
    </div>
</body>

</html>