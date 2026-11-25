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
$trem = null;

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

// A consulta usa JOIN para juntar duas tabelas:
//     - "trem t": apelido "t" para a tabela de trens.
//     - LEFT JOIN: traz o trem mesmo que ele não tenha um usuário associado.
//     (nesse caso, email vem vazio), com um JOIN normaç, trens sem responsável não iriam aparecer.
// Busca os dados do trem e o usuário responsável.

$consulta = $conexao->prepare(
    'SELECT
        t.id,
        t.nome_trem,
        t.modelo_trem,
        t.localizacao_i,
        t.codigo,
        t.capacidade,
        t.ano,
        t.status,
        t.id_usuario,
        u.email
    FROM trem t
    LEFT JOIN usuario u ON t.id_usuario = u.id_usuario
    WHERE t.id = ?'
);

// Se o prepare falhou, interrompe tudo com uma mensagem die() e mostra imediatamente o texto na tela.

if (!$consulta) {
    die('Erro ao consultar o trem.');
}

// Liga o valor de $id ao "?", o "i" significa que é um número inteiro

$consulta->bind_param('i', $id);

// Executa a consulta no banco.

$consulta->execute();

// Pega o resultado e transforma a primeira linha em um array associativo.
// Exemplo: $dadosTrem['nome_trem'], $dadosTrem['email'] etc.
// Se nçao tiver um trem com esse id. $dadosTrem fica como null/false

$resultado = $consulta->get_result();
$dadosTrem = $resultado->fetch_assoc();

// Libera os recursos da consulta.

$consulta->close();

// Se não achou o trem (id que não existe no banco), volta para a lista.

if (!$dadosTrem) {
    header('Location: lista_trem.php');
    exit;
}

// Oganizar os dados em uma array mais legível.
// Copia os dados do banco para o array $trem, usando nomes mais curtos e claros
// (ex.: 'nome_trem' vira 'nome', 'localizacao_i' vira 'localizacao').
// É esse array $trem que o HTML usa lá embaixo.

$trem = [
    'id' => $dadosTrem['id'],
    'nome' => $dadosTrem['nome_trem'],
    'modelo' => $dadosTrem['modelo_trem'],
    'localizacao' => $dadosTrem['localizacao_i'],
    'codigo' => $dadosTrem['codigo'],
    'capacidade' => $dadosTrem['capacidade'],
    'ano' => $dadosTrem['ano'],
    'status' => $dadosTrem['status'],
    'id_usuario' => $dadosTrem['id_usuario'],
    'email' => $dadosTrem['email']
];

// Exclusão (só acontece quando o botão foi clicado, via POST)
// Se a página foi apenas aberta (GET), tudo dentro deste bloco é ignorado.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // REGRA DE NEGÓCIO: se o trem tem um usuário responsável (id_usuario
    // preenchido), a exclusão é BLOQUEADA e uma mensagem é exibida.
    // empty() considera vazio: null, 0, '' e false.

    if (!empty($trem['id_usuario'])) {

        $erro = 'Este trem não pode ser excluído, pois está atribuído a um usuário responsável.';
    } else {

        // Prepara o comando DELETE. O "WHERE id = ?" é ESSENCIAL: 
        // garante que só o trem escolhido seja apagado. Sem ele, os trens seriam apagados

        $exclusao = $conexao->prepare(
            'DELETE FROM trem WHERE id = ?'
        );

        // Se o prepare() funcionou ($exclusao não é false)


        if ($exclusao) {

            // Liga o id ao "?" (tipo "i" = inteiro).


            $exclusao->bind_param('i', $id);

            // Executa o DELETE. Considera sucesso somente se:
            //   1) execute() deu certo e
            //   2) affected_rows > 0, ou seja, pelo menos 1 linha foi mesmo apagada (confirma que o trem existia e foi removido).

            if ($exclusao->execute() && $exclusao->affected_rows > 0) {

                // Sucesso, fecha os comandos e volta para a lista de trens.

                $exclusao->close();

                header('Location: lista_trem.php');

                // exit: interrompe o script para nada mais rodar depois do redirecionamento.

                exit;
            } else {

                // Não apagou. Um motivo comum: o trem está ligado a outras tabelas (ex.: rotas)
                // e o banco impede a exclusão por causa da chave estrangeira (foreign key).

                $erro = 'Não foi possível excluir este trem. Verifique se existem rotas ou registros vinculados a ele.';
            }

            // Libera os recursos do comando DELETE
            // (só chega aqui se NÃO houve sucesso, pois no sucesso o script já terminou no exit).

            $exclusao->close();
        } else {

            // O próprio prepare() falhou (ex.: tabela inexistente).


            $erro = 'Erro ao processar a exclusão no banco de dados.';
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
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// No banco, o status é guardado como código técnico (ex.: "operacao").
// Este array converte cada código no texto bonito que o usuário vê na tela
// (ex.: "Em operação").

$rotulosStatus = [
    'operacao' => 'Em operação',
    'parado' => 'Parado',
    'manutencao' => 'Manutenção',
    'inativo' => 'Inativo'
];

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Excluir trem</title>

    <link
        rel="stylesheet"
        href="../../assets/style/style.css">
</head>

<body>

    <header class="barra-superior">

        <h1>PIA Enterprise</h1>

        <aside class="menu-lateral">
            <nav>
                <a href="../../index.php">Dashboard</a>

                <a href="../sensor/tela-home-sensor.php">
                    Sensores
                </a>

                <a class="ativo" href="lista_trem.php">
                    Trens
                </a>

                <a href="../rotas/tela-home-rotas.php">
                    Rotas
                </a>

                <?php if ($_SESSION['tipo'] === 'administrador'): ?>
                    <a href="../usuario/tela-home-usuario.php">
                        Usuários
                    </a>
                <?php endif; ?>
            </nav>
        </aside>

    </header>

    <main class="area-principal">

        <section class="cabecalho-cadastro-sensor">

            <div class="informacoes-pagina">

                <h1 class="titulo-pagina">
                    Excluir trem
                </h1>

                <p class="descricao-sensores">
                    Confirme os dados do trem antes de prosseguir com a exclusão.
                </p>

            </div>

            <a
                href="lista_trem.php"
                class="botao-voltar-sensores">

                Voltar para trens

            </a>

        </section>

        <section class="painel-excluir-sensor">



            <h2>
                Tem certeza que deseja excluir este trem?
            </h2>

            <p>
                Essa ação não poderá ser desfeita.
            </p>

            <div class="informacoes-sensor-excluir">

                <div>
                    <strong>Nome:</strong>
                    <?= e($trem['nome']) ?>
                </div>

                <div>
                    <strong>Modelo:</strong>
                    <?= e($trem['modelo']) ?>
                </div>

                <div>
                    <strong>Usuário responsável:</strong>
                    <?= !empty($trem['email'])
                        ? e($trem['email'])
                        : 'Nenhum usuário atribuído' ?>
                </div>

                <div>
                    <strong>Localização:</strong>
                    <?= e($trem['localizacao']) ?>
                </div>

                <div>
                    <strong>Código:</strong>
                    <?= e($trem['codigo']) ?>
                </div>

                <div>
                    <strong>Capacidade:</strong>
                    <?= e($trem['capacidade']) ?>
                </div>

                <div>
                    <strong>Ano:</strong>
                    <?= e($trem['ano']) ?>
                </div>

                <div>
                    <strong>Status:</strong>
                    <?= e(
                        $rotulosStatus[$trem['status']]
                            ?? $trem['status']
                    ) ?>
                </div>

            </div>

            <?php if ($erro !== ''): ?>
                <div class="erro-excluir-sensor">
                    <?= e($erro) ?>
                </div>
            <?php endif; ?>

            <div class="botoes-excluir-sensor">

                <form
                    method="POST"
                    action="exclusao_trem.php">

                    <input
                        type="hidden"
                        name="id"
                        value="<?= e($id) ?>">

                    <button
                        class="botao-confirmar-exclusao"
                        type="submit">

                        Excluir trem

                    </button>

                </form>

                <a
                    href="lista_trem.php"
                    class="botao-cancelar-cadastro">

                    Cancelar

                </a>

            </div>

        </section>

    </main>

</body>

</html>