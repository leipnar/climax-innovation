# Climax Innovation - Headless Website

Modern headless website built with Astro and Directus CMS.

## Tech Stack

- **Frontend**: Astro + Tailwind CSS
- **CMS**: Directus (Headless CMS)
- **Deployment**: Hostinger

## Project Structure

```
climax-new/
├── directus/           # Directus CMS configuration
│   ├── docker-compose.yml
│   ├── snapshots/      # Schema snapshots
│   └── .env.example
└── frontend/           # Astro frontend
    ├── src/
    │   ├── components/
    │   ├── layouts/
    │   ├── lib/
    │   └── pages/
    └── package.json
```

## Development

### Prerequisites

- Node.js 18+
- npm or pnpm
- Docker (for local Directus)

### Setup

1. **Frontend**
   ```bash
   cd frontend
   npm install
   npm run dev
   ```

2. **Directus (Local)**
   ```bash
   cd directus
   cp .env.example .env
   # Edit .env with your settings
   docker-compose up -d
   ```

## Deployment

### Frontend (Hostinger)

1. Push code to GitHub repository
2. In Hostinger hPanel, go to Websites > [Your Domain] > Node.js
3. Create new app:
   - Repository: Your GitHub repo
   - Branch: main
   - Root directory: `/frontend`
   - Build command: `npm run hostinger:build`
   - Start command: `npm run hostinger:start`

### Directus Options

**Option A: Directus Cloud (Recommended)**
1. Go to directus.io
2. Create account and new project
3. Import schema from `directus/snapshots/schema.yaml`

**Option B: Hostinger Node.js**
1. Same as frontend deployment
2. Point subdomain (e.g., cms.climaxinnovation.com) to Directus app
3. Set environment variables in hPanel

**Option C: VPS (DigitalOcean, Railway, etc.)**
1. Deploy using Docker Compose
2. Point subdomain to VPS IP

## Content Migration

Content from the old WordPress site can be migrated to Directus:

1. Export WordPress content as JSON/XML
2. Transform to Directus format
3. Import via Directus API

## Performance

This setup achieves A+ performance scores:
- Static HTML generation
- Zero JavaScript by default
- Optimized images
- Minimal CSS with Tailwind

## License

Private - Climax Innovation
