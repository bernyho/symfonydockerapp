# SymfonyDocker app

## Tech stack
- Docker 
- PHP 7.4
- MySQL 8
- Nginx
- Symfony 5

## Installation

1. Clone this repository. `git clone <url>`
2. Run docker-compose. `docker-compose up -d`
3. Go to bash in PHP container `docker exec -it c_php /bin/bash`
- Install composer.  `composer install`
- Run migrations `sf doctrine:migrations:migrate`
  - Type *yes* for continue 😈
6. Open your favourite browser with http://localhost