
# Ferrorama — Sistema de Gerenciamento Ferroviário

O **Ferrorama** é um sistema web de gerenciamento ferroviário desenvolvido como parte da Situação de Aprendizagem do curso Técnico em Desenvolvimento de Sistemas.

O projeto tem como objetivo organizar informações de usuários, trens, rotas e sensores em uma única aplicação.

## 1. Funcionalidades

- **Autenticação:** acesso por e-mail e senha.
- **Perfis de acesso:** administrador e funcionário, com permissões diferentes.
- **Dashboard:** resumo da quantidade de sensores ativos, trens em operação e rotas ativas.
- **Usuários:** gerenciamento de contas realizado pelo administrador.
- **Trens:** cadastro, consulta, edição e exclusão de informações.
- **Rotas:** gerenciamento de rotas ferroviárias.
- **Sensores:** gerenciamento de sensores.
- **Relacionamentos:** associação entre usuários e trens, trens e rotas, e rotas e sensores.
- **Validações:** verificação de campos obrigatórios e informações únicas.
- **Controle de acesso:** proteção das páginas administrativas conforme o perfil do usuário.

As associações entre trem e rota e entre rota e sensor são opcionais.

## 2. Tecnologias utilizadas

- **Front-end:** HTML, CSS e JavaScript
- **Back-end:** PHP
- **Banco de dados:** MySQL
- **Servidor local:** XAMPP
- **Versionamento:** Git e GitHub

## 3. Requisitos para executar o projeto

- XAMPP instalado.
- Apache e MySQL.
- Navegador atualizado.
- Git (opcional).

## 4. Como instalar e executar

### 4.1. Baixar o projeto

Clone o repositório:

```bash
git clone https://github.com/felipepaloschii/sa-ferrorama.git
```

Ou baixe o projeto pelo botão **Code** do GitHub e extraia os arquivos.

### 4.2. Colocar o projeto no servidor local

No Windows, coloque a pasta do projeto dentro de:

```text
C:\xampp\htdocs\
```

### 4.3. Iniciar o XAMPP

Abra o painel do XAMPP e inicie:

- Apache
- MySQL

### 4.4. Criar o banco de dados

1. Acesse `http://localhost/phpmyadmin`.
2. Abra a aba **Importar**.
3. Selecione o arquivo `database/sa-ferrorama.sql`.
4. Execute a importação.

**Atenção:** o script contém `DROP DATABASE IF EXISTS sa_ferrorama_db`, que apaga um banco existente com esse nome antes de recriá-lo. Faça backup antes de executar se houver dados importantes.

O script cria as tabelas sem registros de demonstração.

### 4.5. Configurar a conexão

A conexão está configurada em:

`infra/conexao.php`

Configuração atual:

- Servidor: `localhost`
- Usuário: `root`
- Senha: vazia
- Banco: `sa_ferrorama_db`

Se sua instalação utilizar configurações diferentes, ajuste o arquivo de conexão.

### 4.6. Acessar o sistema

Abra o navegador e acesse:

```text
http://localhost/sa-ferrorama/public/landing.php
```

Se a pasta do projeto tiver outro nome dentro de `htdocs`, ajuste o endereço.

Na página inicial, clique em **Acessar o sistema** para abrir a tela de login.

O banco é criado vazio. É necessário realizar o cadastro inicial de administrador pelo procedimento disponibilizado no projeto.

## 5. Estrutura do projeto

```text
sa-ferrorama/
├── assets/             # Estilos, fontes e imagens
├── database/           # Script SQL do banco
├── infra/              # Conexão e verificações de acesso
├── pesquisas/          # Pesquisas e materiais
├── public/             # Páginas e módulos do sistema
├── script/             # Arquivos JavaScript
├── index.php           # Dashboard
├── MANUAL.md           # Manual de utilização
└── README.md           # Apresentação do projeto
```

## 6. Banco de dados

O banco utilizado é `sa_ferrorama_db`.

| Tabela | Finalidade |
|---|---|
| `usuario` | Armazena contas e perfis de acesso |
| `trem` | Armazena informações dos trens |
| `rotas` | Armazena informações das rotas |
| `sensor` | Armazena informações dos sensores |

Os relacionamentos são definidos por chaves estrangeiras. Alguns vínculos são opcionais, permitindo cadastrar trens sem responsável, rotas sem trem associado e sensores sem rota associada.

## 7. Documentação

Consulte o manual para aprender a acessar e utilizar as funcionalidades do sistema:

[Manual de Utilização — Ferrorama](MANUAL.md)

## 8. Equipe

Projeto desenvolvido por estudantes do curso Técnico em Desenvolvimento de Sistemas, como parte da Situação de Aprendizagem.

## 9. Observação

O Ferrorama é um projeto acadêmico desenvolvido para fins educacionais e de demonstração.
