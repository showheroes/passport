#!/usr/bin/env bash

# Exporting global ENV
echo "Export global ENV variables..."
set -o allexport; source .env; set +o allexport

echo "Starting..."
docker-compose -f docker-compose.yml up
