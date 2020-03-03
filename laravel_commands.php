LARAVEL Basic commands

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

php artisan db:seed --class=UsersTableSeeder

- CREATE TEST

php artisan make:test UserTest --unit


- RUN TEST

./vendor/bin/phpunit

./vendor/bin/phpunit --filter=xxx


