# Weblog

A Laravel-powered blog and content management platform for publishing posts, organizing categories, managing users, and controlling access with role-based permissions.

## Project Overview

Weblog is a modern blog application built with Laravel 12 and PHP 8.2. It includes a full admin workflow for managing posts, categories, authentication, and user roles. The project is designed for a content-driven website where authors can publish content while administrators control permissions and platform access.

## Features

- User registration and login
- JWT-based authentication with cookie support
- Role and permission management
- User promotion and role assignment
- Blog post creation, editing, deletion, and restore support
- Category management
- Dashboard access for authorized users
- Soft deletes for safer data handling
- Jalali date support for Persian/IR date formatting

## Tech Stack

- PHP 8.2
- Laravel 12
- MySQL / database-driven app
- JWT Auth
- Composer
- NPM / Vite
- Jalali date library (`morilog/jalali`)

## Main Modules

- `posts` — create, view, edit, restore, and delete blog posts
- `categories` — manage content categories
- `users` — user administration and role assignment
- `roles` — define roles and permissions
- `panel` — admin dashboard for authorized users

## Getting Started

1. Clone the repository
   ```bash
   git clone https://github.com/saeedjameii/Weblog.git
   cd Weblog
   ```

2. Install PHP dependencies
   ```bash
   composer install
   ```

3. Create the environment file
   ```bash
   cp .env.example .env
   ```

4. Generate the application key
   ```bash
   php artisan key:generate
   ```

5. Configure your database in `.env` and run migrations
   ```bash
   php artisan migrate
   ```

6. Install frontend dependencies and build assets
   ```bash
   npm install
   npm run build
   ```

7. Start the application
   ```bash
   php artisan serve
   ```

8. Open the app in your browser at `http://localhost:8000`

## Project Scripts

```bash
composer run setup
composer run test
npm run dev
```

The `setup` script installs dependencies, creates the `.env` file when needed, generates the app key, runs migrations, and builds frontend assets.

## Development Notes

This project includes permission-based route protection and role-based access checks, making it suitable for a multi-user blog platform with an admin panel and restricted content management features.

## License

This project is open-source and uses the MIT license.
