LARAVEL Basic commands

- CREATE PROJECT 

composer create-project --prefer-dist laravel/laravel blog "5.8.*"

- CREATE ALL
php artisan make:model modelName -a

- CREATE MODEL (& migration)

php artisan make:model Flight -m

- CREATE MIGRATION

php artisan make:migration create_users_table --create=users

php artisan make:migration add_votes_to_users_table --table=users

- MIGRATION TIPS

$table->renameColumn('quantity', 'stock');
$table->string('description')->nullable(true)->change();


- CREATE CONTROLLER (& resource)

php artisan make:controller PhotoController --resource --model=Photo

php artisan make:controller API/PriceController --resource --model=Price

- CREATE FACTORY

php artisan make:factory PostFactory --model=Post

- RUN FACTORY

php artisan tinker

factory(App\User::class)->create();

- CREATE SEEDER

php artisan make:seeder UsersTableSeeder

- RUN specific SEED

artisan db:seed --class=UserTableSeeder

- CREATE TEST

php artisan make:test UserTest --unit


- RUN TEST

./vendor/bin/phpunit

./vendor/bin/phpunit --filter=xxx

- CREATE POLICE

php artisan make:policy PostPolicy

- CREATE RULE

php artisan make:rule RuleName





