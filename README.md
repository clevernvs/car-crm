### O Projeto

Sistema de gestão e revenda de automóveis.

### Tecnologias Utilizadas

- PHP 8.1 - Linguagem de Programação
- Laravel 9 - Framework PHP
- Laravel Passaport - Full OAuth2 server implementation for your Laravel
- MySQL 8.0 - Banco de Dados OpenSource
- Intervention Image - Biblioteca para manipulação de imagens.
- Docker - Container

### - Rodando as Migrations
Para criar as tabelas do Banco de Dados, deverá rodar o seguinte comando:

~~~
$ sail artisan migrate
~~~

### - Rodando as Seeders
Para popular com alguns dados o Banco de Dados, deverá rodar o seguinte comando:

~~~
$ sail artisan db:seed 
~~~
