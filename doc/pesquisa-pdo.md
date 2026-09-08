## Pesquisa – PDO (PHP Data Objects)

1. O que é o PDO?
 É uma extensão do PHP que fornece uma interface leve e consistente para o acesso ao banco de dados.

2. Para que ele é utilizado no PHP?
 Para permitir que aplicações desenvolvidas em PHP possam se conectar e interagir com bancos de dados.

3. Como funciona uma conexão utilizando PDO?
 Funciona através da classe nativa PDO, assim utilizando um DSN para definir o driver, host e o nome do DB junto as credenciais de acesso.

4. Quais são suas principais características.
 Pode trabalhar com diferentes sistemas de anco de dados, entre eles: MySQL, oracle.
 Interface padrão.
 Permite configurar diferentes formas de tratamento de erros.

5. Diferenças entre PDO e MySQLi.
 PDO aceita diversos bancos através de drivers, ja o MySQLi apenas o MySQL.
 Portabilidade do PDO é maior que a MySQLi.
 Sintaxe do PDO é padronizada para diferentes bancos ja a MySQLi é específica para MySQL.

6. Vantagens e desvantagens de utilizar PDO.
 O PDO é mais versátil e tendo uma interface mais padronizada porém, ele tem uma complexidade maior para quem está iniciando.

7. O que são Prepared Statements e por que são importantes.
 É uma forma mais segura de fazer uma inserção ao banco de dados, sendo importante para a prevenção de ataques como o SQL Injection.

8. Em quais situações o PDO pode ser uma boa escolha.
 Projetos que utilizem database diferentes.
 Aplicações que necessitam de de maior portabildade.
 Projetos que priorizam o uso de Prepared Statement.
 
 



 

































































https://www.php.net/manual/pt_BR/book.pdo.php
https://www.php.net/manual/pt_BR/pdo.connections.php
https://www.devmedia.com.br/introducao-ao-php-data-objects-pdo/25318
https://www.treinaweb.com.br/blog/o-que-e-pdo-no-php