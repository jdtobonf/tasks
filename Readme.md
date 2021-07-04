# Task Manager App

### Environment setup

- Make sure you have Docker installed in your system
- PHP and composer intalled in your system
- Use your preferred http client app to test
- Optional: Database viewer

### Installation

- Clone the repository
- Create a `.env` file in the root folder based on `.env.example` and add variable values
- Go to `src` folder and Create a `.env` file based on `.env.example` and change database credentials (make sure they are the same as previous step)
- Run `composer install` (then `composer fund` if asked)
- Add the host `app.local` in your `hosts` file
- Go back to root folder
- Run `docker-compose up -d`
- Open a terminal in the `php` container: `docker-compose exec php bash`
- Load data fixtures inside the container: `php bin/console doctrine:fixtures:load`
- Open your browser and go to https://app.local
- **Optional:** Open Postman collection provided in `resources` folder and configure the environment variables as shown in the image (except token value)