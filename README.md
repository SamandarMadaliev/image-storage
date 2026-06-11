# Image Store API

REST API for uploading, listing, retrieving, and deleting images. API-only — no web UI.

Users authenticate with Laravel Sanctum bearer tokens. Image files are stored in MinIO (S3-compatible storage); metadata is stored in PostgreSQL.

## Features

- User registration and login (Sanctum token auth)
- Upload images (PNG and JPEG only, max 5 MB)
- List, download, and delete your own images
- Daily upload limit per user (default: 100,000 images/day, tracked in Redis)
- Auto-generated OpenAPI documentation (Scramble)

## Tech stack

| Component | Technology |
|-----------|------------|
| Framework | Laravel 13, PHP 8.3+ |
| Auth | Laravel Sanctum |
| Database | PostgreSQL 17 |
| Cache / queues / rate limits | Redis 8 |
| Object storage | MinIO (S3 API) |
| Reverse proxy | Nginx |
| API docs | [Scramble](https://scramble.dedoc.co/) (OpenAPI 3.1) |

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) and Docker Compose

## Getting started (Docker)

### 1. Clone and configure environment

```bash
git clone <repository-url>
cd image-store
cp .env.example .env
```

Edit `.env` and set at least these values for Docker:

```env
APP_KEY=                          # generated in step 2
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=database
DB_PORT=5432
DB_DATABASE=image_storage
DB_USERNAME=admin
DB_PASSWORD=your-db-password

REDIS_HOST=redis

FILESYSTEM_DISK=s3
QUEUE_CONNECTION=redis

AWS_ACCESS_KEY_ID=your-minio-user
AWS_SECRET_ACCESS_KEY=your-minio-password
AWS_BUCKET=images
AWS_ENDPOINT=http://minio:9000
AWS_USE_PATH_STYLE_ENDPOINT=true

MINIO_ROOT_USER=your-minio-user
MINIO_ROOT_PASSWORD=your-minio-password

IMAGE_DAILY_UPLOAD_LIMIT=100000
```

`AWS_ACCESS_KEY_ID` / `AWS_SECRET_ACCESS_KEY` must match `MINIO_ROOT_USER` / `MINIO_ROOT_PASSWORD`.

### 2. Generate application key

```bash
docker compose run --rm backend php artisan key:generate
```

Copy the generated `APP_KEY` into your `.env` if it was not written automatically.

### 3. Start services

```bash
docker compose up -d --build
```

This starts:

| Service | Container | Port |
|---------|-----------|------|
| API (Nginx) | `nginx_image_store` | 80 |
| PHP-FPM | `backend_image_store` | — |
| PostgreSQL | `database_image_store` | 5432 |
| Redis | `redis_image_store` | 6379 |
| MinIO | `minio_image_store` | 9000 (API), 9001 (console) |

Migrations run automatically when the backend container starts.

### 4. Create the MinIO bucket

On first run, create the `images` bucket (name must match `AWS_BUCKET`):

```bash
docker exec minio_image_store mc alias set local http://localhost:9000 your-minio-user your-minio-password
docker exec minio_image_store mc mb local/images --ignore-existing
```

### 5. Verify the API

```bash
curl http://localhost/api
```

Expected response:

```json
{"message":"Welcome to the Image Store API. Please login to use this service."}
```

## API documentation

Interactive Swagger UI (available when `APP_ENV=local`):

- **UI:** http://localhost/docs/api
- **OpenAPI JSON:** http://localhost/docs/api.json

## API overview

### Public endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api` | Welcome message |
| `POST` | `/api/register` | Register a new user |
| `POST` | `/api/login` | Login and receive a token |

### Protected endpoints (Bearer token required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/logout` | Revoke current token |
| `GET` | `/api/user` | Get authenticated user |
| `POST` | `/api/images` | Upload an image |
| `GET` | `/api/images` | List your images |
| `GET` | `/api/images/{id}` | Download image by record ID |
| `DELETE` | `/api/images/{id}` | Delete image by record ID |

`{id}` is the numeric record ID from upload/list responses — not the original file name.

### Quick example

```bash
# Register
curl -X POST http://localhost/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123"}'

# Login
TOKEN=$(curl -s -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"john@example.com","password":"password123"}' \
  | php -r 'echo json_decode(file_get_contents("php://stdin"))->token;')

# Upload
curl -X POST http://localhost/api/images \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" \
  -F "image=@/path/to/photo.png"

# List images
curl http://localhost/api/images \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

## Running commands

All Artisan and Composer commands should be run inside the backend container:

```bash
docker compose exec backend php artisan migrate
docker compose exec backend php artisan test
docker compose exec backend ./vendor/bin/pint
```

## Running tests

```bash
docker compose exec backend php artisan test
```

## Project structure

```
app/
├── DTOs/           # Data transfer objects
├── Http/
│   ├── Controllers/
│   └── Requests/   # Form request validation
├── Repositories/   # Database and storage access
└── Services/       # Business logic
```

## License

MIT
