# Pesquisa sobre APIs (Application Programming Interfaces)

## 1. O que é uma API e qual é sua finalidade?

API é a sigla para *Application Programming Interface*, que em português significa Interface de Programação de Aplicações.

De forma simples, uma API permite que dois ou mais sistemas diferentes troquem informações entre si. Ela funciona como uma ponte de comunicação, permitindo que uma aplicação utilize informações ou recursos de outra sem precisar conhecer todo o seu funcionamento interno.

Um exemplo que podemos encontrar no dia a dia é quando utilizamos um aplicativo de previsão do tempo. Muitas vezes, ele utiliza uma API para buscar informações sobre a temperatura e as condições climáticas.

Nas empresas, as APIs também são bastante utilizadas para conectar sistemas financeiros, controle de estoque, vendas e recursos humanos.

## 2. Como funciona a comunicação entre sistemas por meio de APIs?

A comunicação por meio de APIs acontece quando um sistema precisa de alguma informação ou deseja realizar uma ação em outro sistema.

Esse processo funciona basicamente da seguinte maneira:

1. Um sistema envia uma solicitação para a API.
2. A API recebe essa solicitação e verifica o que foi pedido.
3. O servidor realiza a operação necessária.
4. A API prepara uma resposta com o resultado.
5. O sistema que fez a solicitação recebe e utiliza as informações.

Por exemplo, imagine que uma empresa tenha um sistema para consultar os funcionários. Quando alguém procura pelo cadastro de uma pessoa, o sistema envia uma solicitação para a API, que busca os dados e retorna as informações encontradas.

Assim, os sistemas conseguem trabalhar juntos, mesmo tendo sido desenvolvidos com tecnologias diferentes.

## 3. O que são APIs REST?

REST significa *Representational State Transfer*. É um estilo de arquitetura utilizado na criação de APIs, permitindo que diferentes sistemas se comuniquem utilizando principalmente o protocolo HTTP.

As APIs REST são bastante comuns no desenvolvimento web porque possuem uma estrutura organizada e facilitam a troca de informações.

Elas trabalham com recursos, que podem representar funcionários, clientes, produtos, pagamentos e outras informações de um sistema.

Algumas características das APIs REST são:

- Utilizam os métodos HTTP para realizar operações.
- Geralmente trabalham com dados no formato JSON.
- Permitem a comunicação entre diferentes tecnologias.
- Organizam as informações por meio de endpoints.
- Separam as responsabilidades do cliente e do servidor.

Um exemplo seria uma API de uma loja virtual que permite consultar produtos, cadastrar novas informações e atualizar o estoque.

## 4. O papel dos métodos HTTP

Os métodos HTTP indicam qual ação queremos realizar quando fazemos uma solicitação para uma API.

Os mais utilizados são GET, POST, PUT, PATCH e DELETE.

| Método | Para que serve | Exemplo |
|---|---|---|
| GET | Consultar informações | Listar funcionários |
| POST | Cadastrar informações | Adicionar funcionário |
| PUT | Substituir os dados de um registro | Atualizar um cadastro completo |
| PATCH | Alterar parte dos dados | Modificar somente o cargo |
| DELETE | Excluir informações | Remover um funcionário |

### Exemplos práticos

Para entender melhor, vamos imaginar uma API utilizada para gerenciar os funcionários de uma empresa.

**GET – Consultar informações**

```http
GET /api/funcionarios
```

Esse método é utilizado quando queremos consultar os funcionários cadastrados.

Exemplo de resposta:

```json
{
    "funcionarios": [
        {
            "id": 1,
            "nome": "Carlos",
            "cargo": "Analista"
        },
        {
            "id": 2,
            "nome": "Mariana",
            "cargo": "Assistente"
        }
    ]
}
```

**POST – Cadastrar informações**

```http
POST /api/funcionarios
```

Esse método permite enviar informações para cadastrar um novo funcionário.

Exemplo dos dados enviados:

```json
{
    "nome": "João",
    "cargo": "Auxiliar Administrativo",
    "salario": 1800
}
```

**PUT – Atualizar informações**

```http
PUT /api/funcionarios/1
```

O PUT é utilizado para substituir os dados de um funcionário por novas informações.

```json
{
    "nome": "Carlos Silva",
    "cargo": "Gerente",
    "salario": 4500
}
```

**PATCH – Atualizar parte das informações**

```http
PATCH /api/funcionarios/1
```

Nesse caso, queremos alterar somente o cargo do funcionário, sem precisar enviar todos os outros dados.

```json
{
    "cargo": "Supervisor"
}
```

**DELETE – Excluir informações**

```http
DELETE /api/funcionarios/1
```

Esse método solicita a exclusão do funcionário que possui o identificador 1.

Dessa forma, cada método possui uma finalidade e ajuda a organizar as operações realizadas pela API.

## 5. O que são requisições e respostas HTTP?

As requisições e respostas HTTP são responsáveis pela comunicação entre um sistema e um servidor.

A requisição acontece quando um sistema envia uma solicitação, seja para consultar alguma informação, cadastrar um registro ou realizar outra operação.

Já a resposta é o retorno enviado pelo servidor depois de receber e processar essa solicitação.

Uma requisição pode conter o método utilizado, o endereço do recurso, informações adicionais e os dados que serão enviados.

A resposta normalmente apresenta um código que informa se a operação foi realizada corretamente ou se aconteceu algum erro.

### Principais códigos HTTP

| Código | Significado |
|---|---|
| 200 | Solicitação realizada com sucesso. |
| 201 | Registro criado com sucesso. |
| 204 | Operação concluída sem conteúdo na resposta. |
| 400 | Solicitação enviada incorretamente. |
| 401 | Usuário não autenticado. |
| 403 | Usuário sem permissão de acesso. |
| 404 | Informação ou endereço não encontrado. |
| 500 | Erro interno no servidor. |

Esses códigos são importantes porque ajudam a identificar o resultado de cada solicitação e facilitam a identificação de problemas durante o desenvolvimento.

## 6. O que são endpoints?

Endpoints são os endereços utilizados para acessar os recursos e funcionalidades de uma API.

Podemos entender os endpoints como caminhos que indicam ao sistema onde determinada informação está disponível ou qual operação pode ser realizada.

Por exemplo, em uma API de uma empresa, poderíamos encontrar os seguintes endereços:

```text
GET    /api/funcionarios
GET    /api/funcionarios/1
POST   /api/funcionarios
PUT    /api/funcionarios/1
PATCH  /api/funcionarios/1
DELETE /api/funcionarios/1

GET    /api/produtos
POST   /api/produtos
GET    /api/pagamentos
```

Cada endereço possui uma finalidade. Alguns permitem consultar informações, enquanto outros permitem cadastrar, atualizar ou excluir registros.

Isso facilita a organização da API e torna mais simples a comunicação entre os sistemas.

## 7. Como funciona a troca de dados no formato JSON?

JSON significa *JavaScript Object Notation*. É um formato utilizado para organizar e transmitir informações entre diferentes sistemas.

Ele é bastante utilizado nas APIs porque possui uma estrutura simples e pode ser interpretado por várias linguagens de programação, como PHP, JavaScript, Python e Java.

Veja um exemplo de informações de um funcionário no formato JSON:

```json
{
    "id": 10,
    "nome": "João",
    "cargo": "Jovem Aprendiz",
    "setor": "Financeiro",
    "ativo": true
}
```

Nesse exemplo, cada informação possui uma chave e um valor. Por exemplo, a chave `nome` possui o valor `João`.

O JSON também permite trabalhar com diferentes tipos de dados:

- String: utilizado para textos.
- Number: utilizado para números.
- Boolean: representa verdadeiro ou falso.
- Object: reúne várias informações.
- Array: permite armazenar listas de dados.
- Null: representa a ausência de um valor.

Quando uma API recebe ou envia informações, o JSON ajuda a manter os dados organizados, facilitando sua interpretação pelos sistemas envolvidos.

## 8. Diferenças entre uma API e um sistema web tradicional

Apesar de estarem relacionados ao desenvolvimento web, uma API e um sistema web tradicional possuem funções diferentes.

Um sistema web tradicional é desenvolvido principalmente para que as pessoas possam utilizar suas funcionalidades por meio de telas, menus, botões e formulários.

Já uma API tem como principal objetivo permitir que outros sistemas acessem informações e utilizem determinadas funcionalidades.

Veja algumas diferenças:

| Característica | Sistema web tradicional | API |
|---|---|---|
| Objetivo | Interação com usuários | Comunicação entre sistemas |
| Interface | Páginas e telas | Endpoints |
| Dados retornados | Geralmente HTML | Geralmente JSON |
| Utilização | Navegador | Aplicações e outros sistemas |
| Exemplo | Sistema de controle de estoque | API de produtos |

Um detalhe importante é que uma API também pode trabalhar junto com um sistema web. Por exemplo, uma página de controle de estoque pode utilizar uma API para buscar e atualizar os produtos cadastrados.

## 9. Como uma aplicação PHP pode consumir uma API?

Uma aplicação desenvolvida em PHP consegue acessar APIs de outros sistemas para buscar informações ou enviar dados.

Uma das maneiras de fazer isso é utilizando o cURL, uma ferramenta que permite realizar requisições HTTP.

Para entender melhor, podemos utilizar uma API pública de testes chamada JSONPlaceholder, que disponibiliza informações fictícias para praticar.

### Exemplo utilizando PHP

```php
<?php

$url = "https://jsonplaceholder.typicode.com/users/1";

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$resposta = curl_exec($ch);

if ($resposta === false) {
    die("Erro: " . curl_error($ch));
}

$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($status == 200) {

    $dados = json_decode($resposta, true);

    echo "Nome: " . htmlspecialchars($dados["name"]);
    echo "<br>";
    echo "Email: " . htmlspecialchars($dados["email"]);

} else {
    echo "Erro HTTP: " . $status;
}

?>
```

### Como esse código funciona?

Primeiro, o PHP recebe o endereço da API que será consultada. Depois, utiliza o cURL para enviar uma requisição e receber as informações do servidor.

Como os dados retornam no formato JSON, utilizamos `json_decode()` para transformar essas informações em um array PHP.

Depois disso, conseguimos acessar os dados normalmente, como o nome e o e-mail do usuário.

Também existe a função `json_encode()`, que faz o processo contrário, transformando dados do PHP em JSON.

## 10. Como uma API pode disponibilizar informações para outros sistemas?

Além de consumir informações de outras aplicações, um sistema também pode criar sua própria API para disponibilizar dados.

Isso significa que uma aplicação pode permitir que outros sistemas consultem determinadas informações, desde que tenham autorização para isso.

Imagine uma empresa que possui um sistema desenvolvido em PHP e MySQL para controlar seus funcionários.

Por meio de uma API, ela poderia disponibilizar informações como:

- Nome dos funcionários.
- Cargos e setores.
- Informações de produtos.
- Quantidade de itens no estoque.
- Situação de determinados pagamentos.

### Exemplo em PHP

```php
<?php

header("Content-Type: application/json; charset=utf-8");

$funcionarios = [
    [
        "id" => 1,
        "nome" => "Carlos",
        "setor" => "Financeiro"
    ],
    [
        "id" => 2,
        "nome" => "Mariana",
        "setor" => "Recursos Humanos"
    ]
];

echo json_encode(
    $funcionarios,
    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);

?>
```

Nesse exemplo, criamos uma lista de funcionários e utilizamos `json_encode()` para transformar os dados em JSON.

O cabeçalho informa que a resposta será enviada nesse formato.

Em um sistema real, essas informações poderiam ser buscadas diretamente no banco de dados MySQL, permitindo que outros sistemas consultassem os registros por meio da API.

## 11. Importância da autenticação, autorização e segurança no consumo de APIs

Quando uma API trabalha com informações de uma empresa, é muito importante garantir que os dados não sejam acessados por pessoas sem permissão.

Para isso, existem mecanismos de autenticação, autorização e segurança.

**Autenticação**

É a forma de confirmar a identidade de quem está tentando acessar o sistema.

Por exemplo, quando um funcionário entra em uma aplicação utilizando seu login e senha, o sistema verifica se aquelas informações são válidas.

**Autorização**

Depois de confirmar a identidade, o sistema verifica quais funcionalidades aquele usuário pode utilizar.

Por exemplo, um funcionário do financeiro pode ter acesso aos dados de pagamentos, enquanto outro funcionário pode acessar somente informações do estoque.

**Segurança**

A segurança envolve os cuidados necessários para proteger os dados e evitar acessos indevidos.

Algumas práticas importantes são:

- Utilizar HTTPS para proteger a comunicação.
- Utilizar tokens para controlar o acesso.
- Não compartilhar senhas e chaves de acesso.
- Verificar os dados recebidos pela API.
- Definir permissões para cada usuário.
- Evitar disponibilizar informações confidenciais.
- Registrar atividades importantes do sistema.

Esses cuidados ajudam a manter as informações protegidas e tornam a utilização das APIs mais confiável.

## 12. Exemplos de utilização de APIs em sistemas empresariais

As APIs estão presentes em muitas atividades realizadas pelas empresas. Elas ajudam a conectar diferentes sistemas e facilitam processos que, sem essa integração, poderiam precisar ser feitos manualmente.

Alguns exemplos são:

**Sistemas financeiros:** podem utilizar APIs para consultar pagamentos, integrar serviços bancários e acompanhar transações.

**Controle de estoque:** uma API pode conectar o sistema de vendas ao estoque, atualizando a quantidade de produtos disponíveis sempre que uma venda é realizada.

**Recursos Humanos:** pode permitir o compartilhamento de informações de funcionários entre diferentes sistemas, respeitando as permissões de acesso.

**Sistemas de vendas:** podem utilizar APIs para integrar lojas virtuais com plataformas de pagamento.

**Transporte e logística:** podem utilizar APIs para acompanhar entregas, consultar pedidos e atualizar informações sobre o transporte de produtos.

**Integração empresarial:** uma empresa pode possuir sistemas separados para financeiro, estoque, vendas e RH. Com as APIs, esses sistemas conseguem compartilhar informações e trabalhar de forma integrada.

Esses exemplos mostram como as APIs podem facilitar a rotina das empresas, reduzir tarefas manuais e melhorar a organização dos processos.