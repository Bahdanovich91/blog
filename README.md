# Blogy

A simple, fully functional blog built with pure PHP — no frameworks. Features categories, posts, view tracking, sorting, pagination, and posts.

## Project Structure

```
├── bin/
│   ├── compile-css.php     # Compiles SCSS → public/css/style.css
│   └── seed.php            # Seeds the database
├── docker/
│   ├── mysql/init/         # SQL schema, auto-runs on first container start
│   ├── nginx/              # Nginx virtual host config
├── public/                 # Web root (index.php)
├── scss/                   # SCSS source files
├── src/
│   ├── Controller/         # Controllers
│   ├── Core/               # App, Router, Database, base Controller, etc.
│   ├── Entity/             # Entities
│   ├── Repositories/       # Repositories
│   └── Service/            # Business logic
├── templates/              # Smarty .tpl files
│   └── partials/           # Reusable partials (header, footer, post_card, pagination)
```

## Getting Started

### Prerequisites

- Docker and Docker Compose

### 1. Clone and configure

```bash
git clone https://github.com/Bahdanovich91/blog.git
cd blog
cp .env.example .env
```

The default `.env` values match the Docker Compose service configuration:

```
DB_HOST=mysql
DB_NAME=blogy
DB_USER=blogy_user
DB_PASSWORD=blogy_pass
```

### 2. Start the containers

```bash
docker compose up -d
```

The database schema is created automatically on the first run from `docker/mysql/init/001_schema.sql`.

### 3. Install dependencies

```bash
docker-compose exec php composer install
```

### 4. Seed the database

```bash
docker compose exec php php bin/seed.php
```

### 5. Compile styles

```bash
docker compose exec php php bin/compile-css.php
# or
php bin/compile-css.php
```

### 6. Open the site

Visit [http://localhost:8080](http://localhost:8080)
