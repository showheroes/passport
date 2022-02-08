
## Run commands after successful build

- Running composer - `composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs`

- Clearing cache - `php artisan cache:clear && php artisan config:clear && php artisan clear-compiled`

 - Running migration `php artisan migrate --force`
   
 - Running seeders `php artisan db:seed --class=ShowHeroesTeamSeeder`
