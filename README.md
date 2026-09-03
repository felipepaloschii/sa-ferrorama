# Sistema de Gerenciamento Ferroviário

## Sobre o Projeto

Este repositório foi desenvolvido como parte da Situação de Aprendizagem do curso de Desenvolvimento de Sistemas do SENAI.

O objetivo principal é permitir que os alunos demonstrem, na prática, as habilidades adquiridas ao longo do curso por meio da criação de um sistema completo voltado à mobilidade urbana coletiva, com foco em ferrovias.

## Objetivo

Desenvolver um sistema capaz de gerenciar operações ferroviárias, incluindo:

- Controle de trens 🚄
- Monitoramento de rotas
- Gestão de estações
- Integração com sensores
- Simulação de soluções de mobilidade urbana

## Tecnologias e Conceitos Envolvidos

O projeto envolve diversas áreas do desenvolvimento de sistemas:

- 🧠 **Back-end:** Regras de negócio e processamento de dados
- 🎨 **Front-end:** Interface para interação com o usuário
- 🗄️ **Banco de Dados:** Armazenamento e gerenciamento das informações
- 📡 **Sensores:** Coleta de dados em tempo real (simulada ou real)
- 🔗 **Integração de Sistemas**

## Funcionalidades Esperadas

- Cadastro e gerenciamento de trens
- Controle de horários e itinerários
- Monitoramento em tempo real
- Simulação de tráfego ferroviário
- Alertas e notificações
- Relatórios operacionais

  ## Status do Projeto

Nessa nova etapa do projeto, o objetivo é pegar o código já criado nas fases anteriores — desenvolvido usando apenas HTML, CSS e JavaScript — e começar a usar a linguagem PHP, alterando grande parte dos códigos. Também será implementado o banco de dados, usando a linguagem SQL, e o XAMPP passará a ser utilizado como servidor web local.

Agora o foco do projeto é outro: alterar os códigos e melhorar as funcionalidades, de maneira organizada, utilizando novas metodologias de desenvolvimento, como:

- **SCRUM:** metodologia ágil usada principalmente no desenvolvimento de software, que ajuda equipes a trabalharem de forma organizada, colaborativa e adaptável.
- **Kanban:** sistema visual de gestão de trabalho que usa cartões e colunas em um quadro para controlar o fluxo de tarefas de forma ágil e eficiente.

Todas essas metodologias ajudam a entregar o melhor produto possível, fornecendo organização, qualidade e produtividade.

## Novas ferramentas

- **PHP:** após a parte em HTML das telas estar bem encaminhada, a linguagem PHP será implementada nos códigos, com destaque para as operações de CRUD (Create, Read, Update, Delete). Nesta etapa será implementado o back-end do projeto, contemplando cadastro e administração de entidades como usuários, sensores, entre outras.
- **XAMPP:** acrônimo para X (multiplataforma), A (Apache), M (MariaDB/MySQL), P (PHP) e P (Perl). Serve para rodar sistemas direto na máquina, simulando um servidor real e criando um ambiente controlado para rodar códigos e testar banco de dados, sem precisar colocar o site no ar.
- **Banco de Dados:** funciona como a biblioteca do projeto, armazenando todos os dados cadastrados. Utilizando SQL, serão criados os bancos de dados e suas tabelas; com a conexão feita pelo PHP e o servidor local do XAMPP, as telas funcionarão e o projeto será concluído.

**Conclusão:** todas as metodologias e novas ferramentas utilizadas trabalharão juntas para a conclusão do projeto.\

## Requisitos Funcionais

| ID | Requisito Funcional |
|---|---|
| RF01 | O sistema deve permitir o cadastro de um novo administrador com nome, e-mail e senha. |
| RF02 | O sistema deve impedir o cadastro de dois administradores utilizando o mesmo e-mail. |
| RF03 | O sistema deve disponibilizar uma tela de login com campos de e-mail e senha. |
| RF04 | O sistema deve autenticar o administrador validando as credenciais informadas. |
| RF05 | Somente usuários cadastrados como administradores poderão acessar o sistema. |
| RF06 | O sistema deve bloquear temporariamente o acesso após cinco tentativas de login incorretas. |
| RF07 | O sistema deve exibir uma mensagem de erro quando as credenciais informadas forem inválidas. |
| RF08 | O sistema deve permitir o cadastro de um novo trem com código de identificação, modelo e status. |
| RF09 | O sistema não deve permitir o cadastro de dois trens com o mesmo código de identificação. |
| RF10 | O sistema deve verificar se o código informado já está cadastrado antes de concluir o cadastro. |
| RF11 | O sistema deve permitir definir o status do trem como: em operação, parado, em manutenção ou inativo. |
| RF12 | O sistema deve exibir, na tela de trens, todos os trens cadastrados e seus respectivos status. |
| RF13 | O sistema deve permitir a edição dos dados de um trem já cadastrado. |
| RF14 | O sistema deve permitir a exclusão ou inativação de um trem somente mediante confirmação do administrador. |
| RF15 | O sistema deve restringir o acesso à tela de cadastro de trens apenas a administradores autenticados. |
| RF16 | O sistema deve permitir que o administrador autenticado cadastre um novo sensor. |
| RF17 | O sistema deve exigir a seleção do trem ao qual o sensor será vinculado durante o cadastro. |
| RF18 | O sistema deve exigir a seleção do tipo de sensor entre temperatura, velocidade, vibração ou localização. |
| RF19 | O sistema deve gerar automaticamente um identificador único para cada sensor cadastrado. |
| RF20 | O sistema deve permitir que um mesmo trem possua múltiplos sensores associados. |
| RF21 | O sistema deve exibir, na tela "Sensores", todos os sensores cadastrados. |
| RF22 | O sistema deve exibir o status de cada sensor, podendo ser ativo ou inativo. |
| RF23 | O sistema deve permitir a exclusão ou inativação de um sensor somente mediante confirmação do administrador. |
| RF24 | O sistema deve exibir os dados de temperatura captados pelos sensores em graus Celsius (°C). |
| RF25 | O sistema deve exibir os dados de velocidade captados pelos sensores em quilômetros por hora (km/h). |
| RF26 | O sistema deve exibir os dados de vibração captados pelos sensores em Hz ou m/s². |
| RF27 | O sistema deve exibir, em um mapa, a localização do trem com base nos dados do sensor de localização/GPS. |
| RF28 | O sistema deve atualizar a posição do trem no mapa em tempo real ou near real-time, com base nos dados do sensor de localização. |
| RF29 | O sistema deve emitir um alerta visual quando a temperatura ultrapassar o limite configurado. |
| RF30 | O sistema deve emitir um alerta visual quando a velocidade ultrapassar o limite configurado. |
| RF31 | O sistema deve emitir um alerta visual quando a vibração ultrapassar o limite configurado. |
| RF32 | O sistema deve registrar automaticamente a data e hora do cadastro de sensores e trens. |
| RF33 | O sistema deve armazenar o histórico dos dados coletados pelos sensores para consulta posterior. |

## Tecnologias Utilizadas

- **Back-end:** PHP
- **Banco de Dados:** SQL (MySQL/MariaDB)
- **Servidor Local:** XAMPP
- **Front-end:** HTML, CSS, JavaScript
- **Metodologias:** SCRUM, Kanban
