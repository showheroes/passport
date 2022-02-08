#!/usr/bin/env bash

# Exporting global ENV
echo "Export global ENV variables..."
set -o allexport; source .env; set +o allexport

# BUILD:
docker-compose -f docker-compose.yml build passport-service-testing

#RUN:
#docker-compose -f docker-compose.yml up

#GET IN:
#docker exec -it passport-testing-service-php /bin/sh

# EXECUTE TESTS:
#php8 artisan migrate ; ./vendor/bin/phpunit
