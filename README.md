# Game Lobby Backend (Laravel)

This is the backend API for the Game Lobby System built with Laravel. It manages user sessions, player selections, game logic, and session results.

🔗 **Live API**: [https://lobby.zepaapi.com](https://lobby.zepaapi.com)

---

## 🚀 Features

-   User registration and authentication via API tokens
-   Game session handling (start, end, and result logic)
-   Winner determination logic
-   Leaderboard tracking
-   RESTful JSON API

---

## 📦 Tech Stack

-   PHP 8+
-   Laravel 10+
-   MySQL / PostgreSQL
-   Laravel Sanctum (or token-based auth)

---

## 🛠 Setup Instructions

1.  Clone the repository:
    ```bash
    git clonehttps://github.com/mmsh350/game-lobby-system2.git
    cd game-lobby-backend
    ```
2.  Install dependencies:
    composer install
3.  Copy .env and generate app key:
    ```bash cp .env.example .env
    php artisan key:generate
    ```
4.  Set up your .env:
    Edit your .env file with the correct database and URL:

        APP_NAME=GameLobby
        APP_ENV=local | production
        APP_URL=127.0.0.1:8000 | https://lobby.zepaapi.com
        APP_TIMEZONE=Africa/Lagos

        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=your_database
        DB_USERNAME=your_username
        DB_PASSWORD=your_password

5.  Run migrations:

    ```bash
    php artisan migrate
    ```

6.  Start local server (for testing):

    ```bash
    php artisan serve
    ```

## Example API Routes

Method Endpoint Description

-   POST `/api/register` Register user
-   POST `/api/login` Login and get token
-   POST `/api/join-session` Join game session
-   GET `/api/session-results/{id}` View session results
-   GET `/api/leaderboard` Fetch leaderboard

This backend powers a number-guessing game system where each session is timed. Users can only join once per session, and winners are determined after the session ends.
