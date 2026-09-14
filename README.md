# Cadastro de Livros

Teste técnico: cadastro de Livro, Autor e Assunto, com relatório agrupado por autor.

## Stack

- **PHP 8.4** + **Symfony 8.1** (MVC), **Twig** para as views
- **Doctrine ORM** + **Doctrine Migrations** para persistência e versionamento de schema
- **MySQL 8** como banco de dados
- **Webpack Encore** + **Sass/SCSS** + **Bootstrap 5** para o front-end, com tema visual próprio
  (paleta, tipografia, navbar, cards e tabelas customizados em `assets/styles/app.scss` — não é só
  Bootstrap "puro")
- **KnpPaginatorBundle** para paginação das listagens
- **Dompdf** para exportação do relatório em PDF
- **SecurityBundle** (`form_login`) para autenticação — todas as telas exigem login
- **PHPUnit** para os testes (unitários de entidade/validação e funcionais de controller)
- **Docker** (PHP-FPM + Nginx + MySQL) para todo o ambiente
- **GitHub Actions** (`.github/workflows/ci.yml`) para CI: composer install, build de assets e testes

## Arquitetura

```
docker/
  php/Dockerfile        # PHP-FPM 8.4 + extensões (pdo_mysql, intl, zip, bcmath, opcache) + node/npm
  nginx/default.conf     # Nginx servindo public/ e repassando *.php ao PHP-FPM
  mysql/initdb/          # script que cria o banco de testes (livraria_test) na 1ª subida
docker-compose.yml        # serviços: app (php-fpm), nginx, database (mysql), mailer (mailpit)
compose.override.yaml     # serviço de mailer (Mailpit) para debug de e-mails, se necessário
.github/workflows/ci.yml   # pipeline de CI (composer, build de assets, phpunit)
src/
  Entity/                 # Livro, Autor, Assunto (mapeamento seguindo o modelo de dados)
  Repository/
  Form/                   # LivroType, AutorType, AssuntoType
  Controller/              # CRUDs + RelatorioController (HTML e PDF)
migrations/                # schema + view vw_relatorio_livros
templates/                  # Twig, tema Bootstrap 5 + template dedicado para o PDF
assets/                      # JS/SCSS (Webpack Encore) com tema visual próprio
tests/                        # PHPUnit (25 testes: entidade, validação, CRUD funcional, login)
```

### Modelo de dados

Seguido integralmente conforme especificado (Livro, Autor, Assunto, e as tabelas de
associação N:N `livro_autor` e `livro_assunto`), com o acréscimo do campo `valor` (R$)
em Livro, exigido pelo enunciado.

### Relatório

Uma **view** SQL (`vw_relatorio_livros`, criada via migration) une as 3 tabelas principais.
A página `/relatorio` consulta essa view e agrupa os resultados por autor (um livro com mais
de um autor aparece em cada grupo de autor a que pertence). Em `/relatorio/pdf` o mesmo
conjunto de dados é renderizado como PDF (via Dompdf), com layout próprio.

### Paginação

As listagens de Livro, Autor e Assunto usam `KnpPaginatorBundle` (10 itens por página, tema
Bootstrap 5), evitando carregar a tabela inteira de uma vez conforme a base cresce.

### Tratamento de erros

- Validação de formulário (Symfony Validator) previne a maioria dos erros antes de chegar
  ao banco (campos obrigatórios, formato do ano, valor não-negativo, pelo menos 1 autor/assunto,
  `UniqueEntity` para descrição de assunto duplicada).
- Tratamento específico (não genérico) de exceções de banco: `UniqueConstraintViolationException`
  (assunto duplicado) e `ForeignKeyConstraintViolationException`, além de uma checagem de negócio
  explícita que impede excluir Autor/Assunto vinculado a algum Livro.

## Variáveis de ambiente

O `.env` **não é commitado** neste projeto (só o `.env.example`, sem segredo nenhum) — copie-o e
gere seu próprio `APP_SECRET` antes de subir (veja o passo 1 e 3 de "Como subir o projeto"
abaixo). Segredos reais de produção iriam em `.env.local` (gitignorado). As principais variáveis:

| Variável | Onde | Descrição |
|---|---|---|
| `APP_ENV` | `.env` | Ambiente da aplicação (`dev`, `test`, `prod`) |
| `APP_SECRET` | `.env` / `.env.local` | Chave usada para CSRF, sessão etc. — gere uma própria em produção |
| `DATABASE_URL` | `.env` | DSN do MySQL (`mysql://symfony:symfony@database:3306/livraria?...`) — já apontado para o serviço `database` do Docker Compose |
| `MAILER_DSN` | `.env` | Por padrão `null://null` (não envia e-mails); trocar se for usar o Mailpit |

Em produção, sobrescreva `DATABASE_URL` e `APP_SECRET` via `.env.local` ou variáveis de ambiente
reais do servidor/orquestrador — nunca versione segredos de produção.

## Como subir o projeto

Pré-requisito: Docker e Docker Compose.

```bash
# 1. Criar o .env local a partir do exemplo (o .env não é commitado, cada
#    ambiente cria o seu — veja a seção "Variáveis de ambiente" abaixo)
cp .env.example .env

# 2. Construir as imagens e subir os containers (app, nginx, database, mailer)
docker compose up -d --build

# 3. Gerar um APP_SECRET e colar no .env (qualquer string aleatória serve em dev)
docker compose exec app php -r "echo bin2hex(random_bytes(16));"
# copie a saída acima e cole em APP_SECRET= no arquivo .env

# 4. Rodar as migrations (cria as tabelas, a view do relatório, o usuário de
#    demonstração e alguns livros de exemplo)
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

# 5. Instalar dependências JS e compilar os assets (Bootstrap/SCSS)
docker compose run --rm app npm install
docker compose run --rm app npm run build
```

A aplicação fica disponível em **http://localhost:8080**. Todas as telas exigem login
(`/login`); a migration já semeia um usuário de demonstração:

| E-mail | Senha |
|---|---|
| `admin@teste-spassu.local` | `Livraria@2026` |

Banco MySQL exposto em `localhost:3306` (usuário `symfony` / senha `symfony` / banco `livraria`).
Mailpit (captura de e-mails de teste) fica acessível pela porta mapeada dinamicamente pelo Docker
para o container `mailer` (`docker compose port mailer 8025`).

### Alternativa sem instalar nada: GitHub Codespaces

O repositório já tem `.devcontainer/devcontainer.json`. Em **Code → Create codespace on main**,
o GitHub sobe o `docker-compose.yml` inteiro numa máquina na nuvem e já roda `.env`, migrations
e build dos assets sozinho (via `postCreateCommand`) — a aplicação fica pronta na porta 8080
encaminhada automaticamente, sem precisar de Docker (nem de nada) na sua máquina.

## Rodando os testes

```bash
# cria o banco de testes (já criado automaticamente pelo script em docker/mysql/initdb
# na primeira subida; rode manualmente se subir o banco sem esse volume)
docker compose exec app php bin/console doctrine:database:create --env=test --if-not-exists
docker compose exec app php bin/console doctrine:migrations:migrate --env=test --no-interaction

docker compose exec app php bin/phpunit
```

26 testes: unitários de entidade/validação (`tests/Entity`) e funcionais de CRUD, login e página
de erro (`tests/Controller`, via `WebTestCase`, cobrindo criação, edição, exclusão, validação de
formulário, as regras de erro específicas — assunto duplicado, exclusão bloqueada por vínculo —,
o fluxo de autenticação e a página 404 customizada).

## Integração contínua

`.github/workflows/ci.yml` roda em cada push/PR para `main`: sobe um MySQL de serviço, cria o
`.env`, `composer install`, `npm ci && npm run build`, roda as migrations no banco de teste e
`php bin/phpunit`.

## Comandos úteis

```bash
# logs da aplicação
docker compose exec app tail -f var/log/dev.log

# console Symfony
docker compose exec app php bin/console

# acessar o MySQL
docker compose exec database mysql -usymfony -psymfony livraria

# parar tudo
docker compose down

# parar e apagar os dados do banco (destrutivo)
docker compose down -v
```
