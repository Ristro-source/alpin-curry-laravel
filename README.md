# Alpin Curry

## Deploy (production VPS)

```sh
./deploy.sh
```

Pulls `main`, builds the Vite assets in a throwaway `node:24-slim` container (the Docker image does not build them), then rebuilds and restarts the app container. Never run `docker compose up --build` alone: the site returns 500 without `public/build`.

## Unlisted menu pages

`public/m/<token>/` holds static, `noindex` menu versions that are shared by link only (not in the nav, sitemap or robots.txt). Each version gets its own random token folder and stays live until its folder is deleted by hand.

- Current: `/m/b8be6bab04/` (interactive menu, 2026-11-22)

New version: `mkdir public/m/$(openssl rand -hex 5)`, copy the files in, commit, push, `./deploy.sh`.

## Database

SQLite only: `database/database.sqlite`, bind-mounted from the host, so rebuilds keep it.
