# Proprli App 🏠

Backend PHP-8/Laravel project for Proprli.

## Tools 🛠️

- PHP v8.1.0
- Composer v2.8.3
- Laravel v10.10

## How to start (development build using Laravel Sail)

To start properly you must have PHP v8.1.0 and Composer v2.8.3 installed on your environment. I used the ASDF runtime manager to deal with multiple programming languagens and it's versions.

```bash
cp .env.example .env # Creates a new environment variables file

composer install # Installs the dependencies

php artisan key:generate # Generates a new APP_KEY value and stores it in .env

./vendor/bin/sail up # Creates and starts both laravel and psql containers using Sail

./vendor/bin/sail artisan migrate:fresh --seed # Executes database migrations and triggers seeders
```

## Format project files

```bash
./vendor/bin/pint # Formats all project files using PSR-12 standards
```

----------
Released in 2024 by [Victor B. Fiamoncini](https://github.com/Victor-Fiamoncini) ☕
