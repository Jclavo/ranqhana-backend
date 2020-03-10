LARAVEL Basic commands

- CREATE PROJECT 

composer create-project --prefer-dist laravel/laravel blog "5.8.*"

- CREATE MODEL (& migration)

php artisan make:model Flight -m

- CREATE MIGRATION

php artisan make:migration create_users_table --create=users

php artisan make:migration add_votes_to_users_table --table=users


- CREATE CONTROLLER (& resource)

php artisan make:controller PhotoController --resource --model=Photo

- CREATE FACTORY

php artisan make:factory PostFactory --model=Post

- CREATE SEEDER

php artisan make:seeder UsersTableSeeder

- RUN specific SEED

artisan db:seed --class=UserTableSeeder

- CREATE TEST

php artisan make:test UserTest --unit


- RUN TEST

./vendor/bin/phpunit

./vendor/bin/phpunit --filter=xxx


