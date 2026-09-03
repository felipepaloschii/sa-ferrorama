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
| :--- | :--- |
| **RF01** | O sistema deve permitir o cadastro de usuários. |
| **RF02** | O sistema deve permitir login de usuários. |
| **RF03** | O sistema deve validar e-mail e senha. |
| **RF04** | O sistema deve permitir recuperação de senha. |
| **RF05** | O sistema deve permitir logout seguro. |
| **RF06** | O sistema deve permitir diferentes níveis de acesso (administrador e operador). |
| **RF07** | O sistema deve exibir lista de sensores cadastrados. |
| **RF08** | O sistema deve cadastrar novos sensores. |
| **RF09** | O sistema deve editar informações de sensores. |
| **RF10** | O sistema deve excluir sensores. |
| **RF11** | O sistema deve impedir exclusão de sensores com dados associados. |
