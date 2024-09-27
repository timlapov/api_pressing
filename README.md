# Propre-Propre - Backend

This is the backend part of the Propre-Propre project, developed using Symfony 6.4 and API Platform.

## Requirements

- Docker
- Docker Compose

## Installation and Running

1. Clone the repository:

```git clone https://github.com/timlapov/api_pressing.git```

```cd api-pressing```
2.  Create the `.env` file and adjust the variables if needed:
    ```aiignore
    PROJECT_NAME=api-pressing
    MYSQL_DATABASE=api-pressing
    MYSQL_USER=docker
    MYSQL_PASSWORD=_____
    MYSQL_ROOT_PASSWORD=_____
    DATABASE_URL="mysql://$MYSQL_USER:$MYSQL_PASSWORD@mysql:3306/$MYSQL_DATABASE?serverVersion=8.0" 
    ```
3. Build and run the Docker containers:

```docker-compose up -d```
4. Install dependencies and set up the database:
   ```aiignore
   docker-compose exec php-cli composer install
   docker-compose exec php-cli php bin/console doctrine:migrations:migrate
   docker-compose exec php-cli php bin/console doctrine:fixtures:load
   ```
The API will be available at `http://localhost:8081/api`.

## Development

For development purposes, you can use the following commands:

- To run Symfony console commands:
```docker-compose exec php-cli php bin/console [command]```
- To access the MySQL database: 
```docker-compose exec mysql mysql -u [user] -p```
- To access the PhpMyAdmin:
  ```http://localhost:8082```

## Project Structure

- `src/Entity/` - Doctrine entities
- `src/Controller/` - controllers
- `src/DataFixtures/` - fixtures for loading test data
- `config/` - configuration files

## API Documentation

API documentation is available at `/doc` after starting the server.

## Additional Information

For more information about Symfony, visit the [official Symfony documentation](https://symfony.com/doc/current/index.html).

For information about API Platform, refer to the [API Platform documentation](https://api-platform.com/docs/).