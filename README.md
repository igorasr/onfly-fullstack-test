# Onfly Fullstack Test

API Laravel para gerenciamento de pedidos de viagem com autenticação JWT.

## Sumário

- [Stack](#stack)
- [Requisitos](#requisitos)
	- [Local (sem Docker)](#local-sem-docker)
	- [Docker](#docker)
- [Como subir a aplicação (Backend)](#como-subir-a-aplicação-backend)
	- [1) Ambiente local (sem Docker)](#1-ambiente-local-sem-docker)
	- [2) Ambiente Docker](#2-ambiente-docker)
- [Rodando testes](#rodando-testes)
- [Autenticação](#autenticação)
- [Payloads e respostas](#payloads-e-respostas)
- [Regras de negócio](#regras-de-negócio)
- [Como rodar o Front-end](#como-rodar-o-front-end)
	- [Desenvolvimento](#desenvolvimento)
	- [Build de produção](#build-de-produção)
	- [Preview da build](#preview-da-build)

## Stack

- PHP 8.2+
- Laravel 12
- MySQL 8
- Redis
- PHPUnit

## Requisitos

### Local (sem Docker)

- PHP 8.2+
- Composer
- Node.js 20+
- NPM
- MySQL 8
- Redis

### Docker

- Docker + Docker Compose

## Como subir a aplicação (Backend)

### 1) Ambiente local (sem Docker)

```bash
cp .env.example .env
composer install
npm install
```

Configure o `.env` para seu banco/redis locais (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `REDIS_HOST`).

```bash
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan serve
```

API disponível em: `http://localhost:8000`

> Alternativa para subir backend + queue + logs + vite: `composer run dev`

### 2) Ambiente Docker

```bash
cp .env.example .env
```

No `.env`, use os valores para comunicação entre containers:

```dotenv
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=admin
DB_PASSWORD=root
REDIS_HOST=redis
```

Suba os containers e prepare a aplicação dentro do container `app`:

```bash
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan jwt:secret
docker compose exec app php artisan migrate
```

API disponível em: `http://localhost:8989`

## Rodando testes

```bash
php artisan test --compact
```

## Autenticação

Guard usado: `api` com driver `jwt`.

- Rotas públicas:
	- `POST /api/register`
	- `POST /api/login`
- Rotas autenticadas:
	- `GET /api/me`
	- `POST /api/logout`
	- `PATCH /api/travel-requests/{id}/status`
	- `GET|POST|PUT|PATCH|DELETE /api/travel-requests`

Use o token JWT no header:

```http
Authorization: Bearer SEU_TOKEN
```

## Payloads e respostas

Considere:

```bash
BASE_URL=http://localhost:8989
```

Se estiver em local sem Docker, use `http://localhost:8000`.

### 1) Registrar usuário

`POST /api/register`

Payload:

```json
{
	"name": "John Doe",
	"email": "john@example.com",
	"password": "password123",
	"password_confirmation": "password123"
}
```

Resposta `200` (exemplo):

```json
{
	"status": "success",
	"message": "User created successfully",
	"user": {
		"id": 1,
		"name": "John Doe",
		"email": "john@example.com",
		"is_admin": false,
		"created_at": "2026-03-03T12:00:00.000000Z",
		"updated_at": "2026-03-03T12:00:00.000000Z"
	},
	"authorization": {
		"token": "<jwt>",
		"type": "bearer"
	}
}
```

### 2) Login

`POST /api/login`

Payload:

```json
{
	"email": "john@example.com",
	"password": "password123"
}
```

Resposta `200` (exemplo):

```json
{
	"status": "success",
	"user": {
		"id": 1,
		"name": "John Doe",
		"email": "john@example.com",
		"is_admin": false,
		"created_at": "2026-03-03T12:00:00.000000Z",
		"updated_at": "2026-03-03T12:00:00.000000Z"
	},
	"authorization": {
		"token": "<jwt>",
		"type": "bearer"
	}
}
```

Resposta `401` (credenciais inválidas):

```json
{
	"status": "error",
	"message": "Unauthorized"
}
```

### 3) Me

`GET /api/me`

Resposta `200`: objeto do usuário autenticado.

### 4) Logout

`POST /api/logout`

Resposta `200`:

```json
{
	"status": "success",
	"message": "Successfully logged out"
}
```

### 5) Criar pedido de viagem

`POST /api/travel-requests`

Payload:

```json
{
	"destination": "Recife",
	"departure_date": "2026-04-10",
	"return_date": "2026-04-15"
}
```

Resposta `201` (formato `TravelRequestResource`):

```json
{
	"data": {
		"id": 10,
		"requester_id": 1,
		"requester_name": "John Doe",
		"destination": "Recife",
		"departure_date": "2026-04-10T00:00:00.000000Z",
		"return_date": "2026-04-15T00:00:00.000000Z",
		"status": "requested",
		"requester": {
			"id": 1,
			"name": "John Doe",
			"email": "john@example.com"
		}
	}
}
```

### 6) Listar pedidos (com filtros)

`GET /api/travel-requests`

Filtros válidos:

- `status`: `requested`, `approved`, `cancelled`
- `destination`: busca parcial
- `created_from`, `created_to`: período de criação
- `travel_from`, `travel_to`: período de viagem

Exemplo:

```bash
curl -X GET "$BASE_URL/api/travel-requests?status=requested&destination=Paulo&travel_from=2026-05-01&travel_to=2026-05-30" \
	-H "Authorization: Bearer SEU_TOKEN"
```

Resposta `200`:

```json
{
	"data": [
		{
			"id": 10,
			"requester_id": 1,
			"requester_name": "John Doe",
			"destination": "São Paulo",
			"departure_date": "2026-05-10T00:00:00.000000Z",
			"return_date": "2026-05-14T00:00:00.000000Z",
			"status": "requested",
			"requester": {
				"id": 1,
				"name": "John Doe",
				"email": "john@example.com"
			}
		}
	]
}
```

### 7) Consultar por ID

`GET /api/travel-requests/{id}`

Resposta `200`: mesmo formato do item de criação (`data` com `TravelRequestResource`).

### 8) Atualizar pedido

`PUT/PATCH /api/travel-requests/{id}`

Payload permitido (todos opcionais):

- `destination`
- `departure_date`
- `return_date`

Campos proibidos neste endpoint:

- `status`
- `requester_name`
- `requester_id`

### 9) Atualizar status do pedido

`PATCH /api/travel-requests/{id}/status`

Payload:

```json
{
	"status": "approved"
}
```

Valores permitidos para `status`:

- `approved`
- `cancelled`

Resposta `200`: formato `TravelRequestResource`.

### 10) Remover pedido

`DELETE /api/travel-requests/{id}`

Resposta `204` (sem corpo).

## Regras de negócio

- Status inicial ao criar pedido: `requested`.
- Transições de status permitidas:
	- `requested -> approved`
	- `requested -> cancelled`
- Transições não permitidas:
	- qualquer transição a partir de `approved`
	- qualquer transição a partir de `cancelled`
	- manter o mesmo status atual
- Apenas usuário `is_admin = true` pode atualizar status.
- Ao atualizar status com sucesso, o solicitante recebe notificação por e-mail.
- Atualização comum (`PUT/PATCH /travel-requests/{id}`) não permite alterar `status` nem dados de solicitante.

## Como rodar o Front-end

O front-end está na pasta `frontend` e usa Vue + Vite.

Pré-requisitos:

- Node.js `^20.19.0` ou `>=22.12.0`
- NPM

```bash
cd frontend
npm install
```

### Desenvolvimento

```bash
npm run dev
```

Abra a URL exibida no terminal (geralmente `http://localhost:5173`).

### Build de produção

```bash
npm run build
```

### Preview da build

```bash
npm run preview
```

