# Onfly Fullstack Test

API Laravel para gerenciamento de pedidos de viagem, com autenticação JWT, políticas de autorização e regras de negócio em camada de serviço.

## Stack

- PHP 8.2+
- Laravel 12
- MySQL 8
- Redis
- PHPUnit

## Requisitos

- PHP 8.2+
- Composer
- Node.js 20+
- NPM
- MySQL 8 e Redis (para execução local)

## Como rodar o projeto

### Opção 1: Local (sem Docker)

1. Clone o projeto e entre na pasta.
2. Instale dependências backend:

```bash
composer install
```

3. Configure ambiente:

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

4. Ajuste as credenciais do banco no `.env`.
5. Rode migrations:

```bash
php artisan migrate
```

6. Instale dependências frontend (Vite):

```bash
npm install
```

7. Suba a aplicação:

```bash
composer run dev
```

> A API ficará disponível em `http://localhost:8000` (modo local padrão do `artisan serve`).

### Opção 2: Docker

1. Configure o `.env`:

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

2. Suba os containers:

```bash
docker compose up -d --build
```

3. Rode migrations dentro do container da aplicação:

```bash
docker compose exec app php artisan migrate
```

> A API ficará disponível em `http://localhost:8989`.

## Rodando testes

```bash
php artisan test --compact
```

## Autenticação

O projeto usa `guard` `api` com driver JWT.

- Rotas públicas:
	- `POST /api/register`
	- `POST /api/login`
- Rotas autenticadas:
	- `GET /api/me`
	- `POST /api/logout`
	- CRUD de `travel-requests`

Após login/registro, envie o token no header:

```http
Authorization: Bearer SEU_TOKEN
```

## Exemplos de requisição

Considere:

```bash
BASE_URL=http://localhost:8989
```

Se estiver rodando local sem Docker, use `http://localhost:8000`.

### 1) Registrar usuário

```bash
curl -X POST "$BASE_URL/api/register" \
	-H "Content-Type: application/json" \
	-d '{
		"name": "John Doe",
		"email": "john@example.com",
		"password": "password123",
		"password_confirmation": "password123"
	}'
```

### 2) Login

```bash
curl -X POST "$BASE_URL/api/login" \
	-H "Content-Type: application/json" \
	-d '{
		"email": "john@example.com",
		"password": "password123"
	}'
```

### 3) Usuário autenticado (`/me`)

```bash
curl -X GET "$BASE_URL/api/me" \
	-H "Authorization: Bearer SEU_TOKEN"
```

### 4) Logout

```bash
curl -X POST "$BASE_URL/api/logout" \
	-H "Authorization: Bearer SEU_TOKEN"
```

### 5) Criar pedido de viagem

```bash
curl -X POST "$BASE_URL/api/travel-requests" \
	-H "Authorization: Bearer SEU_TOKEN" \
	-H "Content-Type: application/json" \
	-d '{
		"destination": "Recife",
		"departure_date": "2026-04-10",
		"return_date": "2026-04-15"
	}'
```

### 6) Consultar pedido por ID

```bash
curl -X GET "$BASE_URL/api/travel-requests/1" \
	-H "Authorization: Bearer SEU_TOKEN"
```

### 7) Listar pedidos com filtros

Filtros disponíveis:

- `status`: `requested`, `approved`, `cancelled`
- `destination`: texto parcial
- `created_from`, `created_to`: período de criação
- `travel_from`, `travel_to`: período de viagem

Exemplo:

```bash
curl -X GET "$BASE_URL/api/travel-requests?status=requested&destination=Recife&travel_from=2026-04-01&travel_to=2026-04-30" \
	-H "Authorization: Bearer SEU_TOKEN"
```

### 8) Atualizar pedido (sem alterar status)

```bash
curl -X PUT "$BASE_URL/api/travel-requests/1" \
	-H "Authorization: Bearer SEU_TOKEN" \
	-H "Content-Type: application/json" \
	-d '{
		"destination": "Fortaleza",
		"departure_date": "2026-05-01",
		"return_date": "2026-05-10"
	}'
```

### 9) Atualizar status do pedido

Endpoint:

- `PATCH /api/travel-requests/{id}/status`

Payload permitido:

- `approved`
- `cancelled`

Exemplo:

```bash
curl -X PATCH "$BASE_URL/api/travel-requests/1/status" \
	-H "Authorization: Bearer SEU_TOKEN" \
	-H "Content-Type: application/json" \
	-d '{
		"status": "approved"
	}'
```

### 10) Remover pedido

```bash
curl -X DELETE "$BASE_URL/api/travel-requests/1" \
	-H "Authorization: Bearer SEU_TOKEN"
```

## Regras de negócio importantes

- Status inicial do pedido: `requested`.
- Somente usuário administrador pode alterar status.
- usuário que fez o pedido não pode alterar o status do mesmo, somente um usuário administrador.
- Não é permitido cancelar pedido já aprovado.
- Sempre que o status mudar para `approved` ou `cancelled`, o solicitante recebe notificação.

## Estrutura principal

- `app/Http/Controllers/AuthController.php`: fluxo de registro/login/me/logout.
- `app/Http/Controllers/TravelRequestController.php`: endpoints de pedidos.
- `app/Services/TravelRequestService.php`: regras de negócio dos pedidos.
- `app/TravelRequestFiltersData.php`: estrutura de filtros da listagem.
- `app/Policies/TravelRequestPolicy.php`: autorização de acesso.

