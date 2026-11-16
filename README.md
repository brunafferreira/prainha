# Prainha Vôlei

Sistema de gerenciamento para uma escola de vôlei de praia, construído com Laravel 12, PHP, MySQL e Livewire 3. Inclui cadastro e login, painel com indicadores, gerenciamento de alunos, turmas e mensalidades, além de reservas públicas e administrativas da quadra. As reservas têm duração de duas horas, confirmação imediata e respeitam o expediente de segunda a sexta (08h–22h) e sábado e domingo (08h–18h).

## Requisitos

- PHP 8.2 ou superior e Composer
- MySQL 8.0 ou compatível
- Node.js e npm
- Extensão PHP `pdo_mysql` habilitada (e `pdo_sqlite` para executar os testes)

## Instalação

1. Instale as dependências PHP e JavaScript:

   ```sh
   composer install
   npm install
   ```

2. Crie o arquivo de ambiente, gere a chave e configure o banco:

   ```sh
   cp .env.example .env
   php artisan key:generate
   ```

   Ajuste `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` e `DB_PASSWORD` no `.env` para os dados do seu MySQL. Crie o banco `prainha_volei` antes de executar as migrações.

   Compartilhe o endereço `http://localhost:8000/reservar` para que clientes reservem um horário sem criar uma conta. Cada reserva tem duração de duas horas e é confirmada imediatamente. O horário fica bloqueado para a única quadra disponível; exclusão ou cancelamento pelo painel libera esse horário.

3. Crie as tabelas e compile os assets:

   ```sh
   php artisan migrate
   npm run build
   ```

4. Crie uma conta na tela inicial e entre no painel. Inicie o servidor local:

   ```sh
   php artisan serve
   ```

   Acesse `http://localhost:8000`.

Para desenvolver com recarga de assets, use `npm run dev` em um terminal separado.

## Testes

```sh
php artisan test
```
