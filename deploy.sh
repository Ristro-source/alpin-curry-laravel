#!/bin/sh
set -e
cd "$(dirname "$0")"
git pull --ff-only
git submodule update --init --recursive
docker run --rm -v "$PWD":/app -w /app node:24-slim sh -c "npm ci --no-audit --no-fund && npm run build"
test -f public/build/manifest.json
docker compose -f docker-compose.production.yml up -d --build
