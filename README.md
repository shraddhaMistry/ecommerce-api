> ### Ecommerce Laravel API

----------

# Getting started

## Installation

Clone the repository

    git clone <repository-url>

Switch to the repo folder

    cd ecommerce-api

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Generate a new Passport authentication secret key

    php artisan passport:install
        
Start the local development server

    php artisan serve

You can now access the server at http://127.0.0.1:8000