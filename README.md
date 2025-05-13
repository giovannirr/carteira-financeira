# Carteira Financeira

Aplicação desenvolvida em Laravel para controle de carteira digital, com funcionalidades de depósito, transferência entre usuários e gerenciamento de transações. Desenvolvida com Livewire, Filament 3 e Laravel 12.

## Tecnologias utilizadas

- PHP 8.3
- Laravel 12
- Livewire
- Filament 3
- Tailwind CSS
- SQLite (ou MySQL, configurável)
- Node.js (Vite para assets)

---

## Requisitos

- PHP >= 8.2
- Composer
- Node.js >= 18
- NPM ou Yarn
- Banco de dados SQLite ou MySQL

---

## Instalação

1. Clone o repositório:

- git clone https://github.com/giovannirr/carteira-financeira.git
- cd carteira-financeira

2. Instale as dependências PHP:

- composer install

3. Instale dependências do frontend:

- npm install

4. Copie e configure o arquivo .env:

- cp .env.example .env

- Edite o .env conforme seu ambiente (por exemplo, definindo o banco de dados, e-mail, etc).

5. Gere a chave da aplicação:

- php artisan key:generate

6. Rode as migrations e seeders:

- php artisan migrate --seed

7. Compile os assets:

- npm run dev (Para desenvolvimento)

- npm run build (Para dprodução)


Execução local

Execute o servidor: 

- php artisan serve


Usuário Administrador de teste (criados pelo seeder)

Admin (Area de acesso): http://localhost:8000/admin

E-mail: admin@admin.com

Senha: admin


Acesso criação de Carteira: http://localhost:8000/register

Acesso a Carteira: http://localhost:8000/login