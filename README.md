# ashickey

A full-stack blog platform focusing on current issues, tech, beginner development tutorials, and general posting.

Built by **ashraf_azmi**.

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Frontend (Public Blog)** | Flutter Web (Dart) — Material Design 3, Apple Blue accent, dark/light theme |
| **Admin Panel + API** | Laravel 13 — Livewire, Custom TrafficTracker Telemetry, Native DB Analytics |
| **Database** | MongoDB 7 Community |
| **Reverse Proxy** | Nginx |
| **Containerization** | Docker Compose |

---

## Architecture

```text
                    ┌─────────────────────────┐
                    │      Nginx (Port 80)     │
                    └────┬──────┬──────┬───────┘
                         │      │      │
              /          │      │      │  /kcg5025/*
        (Flutter)        │      │      │  /api/*
                         │      │      │
              ┌──────────┘      │      └──────────┐
              ▼                 │                  ▼
   ┌──────────────────┐        │       ┌──────────────────┐
   │  Flutter Web     │        │       │  Laravel 13      │
   │  (Static Build)  │        │       │  (Admin + API)   │
   │  Served by Nginx │        │       │  Port 3000       │
   └──────────────────┘        │       └────────┬──────────┘
                               │                │
                               │                ▼
                               │     ┌──────────────────┐
                               └────▶│  MongoDB 7       │
                                     │  (internal only) │
                                     └──────────────────┘
```

---

## Getting Started

### Prerequisites

- Docker & Docker Compose

### Setup

```bash
# Clone
git clone git@github.com:capgenheim/ashickey.git
cd ashickey

# Configure environment
cp .env.example .env
# Edit .env with your secrets

# Build and run
docker compose up --build
```

### Access

| URL | Description |
|---|---|
| `http://localhost` | Public blog (Flutter Web) |
| `http://localhost/kcg5025` | Admin panel (Next.js) |
| `http://localhost/api/health` | API health check |

### Default Admin Credentials

Set via `.env` file:
- **Email:** `admin@ashickey.com`
- **Password:** `Admin@Ashickey2024!`

> Change these immediately in production.

---

## API Routes

### Public (no auth required)

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/health` | Health check |
| `GET` | `/api/posts` | List published posts (paginated) |
| `GET` | `/api/posts?category={slug}` | Filter posts by category |
| `GET` | `/api/posts?tag={slug}` | Filter posts by tag |
| `GET` | `/api/posts?q={search}` | Full-text search posts |
| `GET` | `/api/posts?cursor={id}&limit={n}` | Cursor-based pagination |
| `GET` | `/api/posts/{slug}` | Get single post by slug |
| `GET` | `/api/categories` | List all categories |
| `GET` | `/api/tags` | List all tags |

### Admin (Bearer token required)

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/auth` | Login — returns access token |
| `PUT` | `/api/auth` | Refresh access token |
| `DELETE` | `/api/auth` | Logout |
| `GET` | `/api/posts?admin=true` | List all posts (including drafts) |
| `POST` | `/api/posts` | Create new post (markdown body) |
| `PUT` | `/api/posts/{id}` | Update post |
| `DELETE` | `/api/posts/{id}` | Delete post |
| `POST` | `/api/categories` | Create category |
| `POST` | `/api/tags` | Create tag |
| `GET` | `/api/media` | List uploaded media |
| `POST` | `/api/media` | Upload image (multipart/form-data) |

### Authentication Flow

```
POST /api/auth  { email, password }
  → { accessToken, user }

# Use access token in header:
Authorization: Bearer <accessToken>

# Refresh when expired:
PUT /api/auth  (uses HttpOnly refresh cookie)
  → { accessToken }
```

---

## Project Structure

```
ashickey/
├── docker-compose.yml
├── .env / .env.example
├── nginx/                    # Reverse proxy config
│   ├── Dockerfile
│   └── nginx.conf
├── frontend/                 # Flutter Web (public blog)
│   ├── Dockerfile
│   ├── pubspec.yaml
│   └── lib/
│       ├── main.dart
│       ├── config/           # Theme (M3 + Apple Blue), routes
│       ├── models/           # Post, Category, Tag
│       ├── services/         # API client, theme persistence
│       ├── screens/          # Home, PostDetail, Categories, Search, About
│       └── widgets/          # PostCard, HeroBanner, Shimmer, NavBar, Footer
├── admin/                    # Next.js (admin + REST API)
│   ├── Dockerfile
│   ├── package.json
│   └── src/
│       ├── app/              # Pages: dashboard, login, posts, categories, media
│       │   └── api/          # REST API routes
│       ├── components/       # Sidebar, MarkdownEditor, PostTable, DashboardStats
│       └── lib/              # MongoDB connection, models, auth, rate-limit, sanitize
├── mongo/
│   └── init-mongo.js         # DB init: user, collections, indexes, seed data
└── uploads/                  # Uploaded media (volume-mounted)
```

---

## Security

- JWT authentication (15min access + 7d refresh tokens, HttpOnly cookies)
- bcrypt password hashing (12 rounds)
- Rate limiting (100 req/min public, 30 req/min auth)
- CORS whitelist
- Input sanitization (sanitize-html + Zod validation)
- Nginx security headers (CSP, X-Frame-Options, HSTS, X-Content-Type-Options)
- MongoDB auth enabled, internal network only (not exposed to host)
- Non-root Docker containers
- Admin path obscured (`/kcg5025`)

---

## Features

### Public Blog (Flutter Web)
- Material Design 3 with Apple Blue (`#007AFF`) accent
- Dark / light theme toggle with smooth animated transition
- Staggered post card animations
- Hero banner with parallax effect
- Shimmer skeleton loading
- Markdown rendering for post content
- Full-text search
- Category & tag filtering
- Responsive layout (mobile / tablet / desktop)
- In-memory API caching + cursor-based pagination

### Admin Panel (Next.js)
- Dashboard with post stats
- Markdown WYSIWYG editor for creating/editing posts
- Post management (create, edit, delete, draft/publish)
- Category & tag management
- Media upload with file type/size validation
- Clean Apple Blue themed UI

---

## License

Private project by ashraf_azmi.
