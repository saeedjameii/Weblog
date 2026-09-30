<h1 align="center">
  Laravel Advanced Weblog ✍️
</h1>

<p align="center">
  <strong>A fully-featured, robust, and scalable weblog platform built with Laravel.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

<hr>

## 📖 About The Project

This is a comprehensive Weblog application designed to provide a seamless experience for both readers and content creators. Under the hood, it leverages the power of Laravel with an advanced architecture including **JWT-based authentication (stored securely in cookies)**, a dynamic **Role-Based Access Control (RBAC)** system, and **hierarchical categorization**. 

Specially tailored for localization, it includes built-in support for **Jalali dates**, making it a perfect fit for Persian/Iranian users.

## ✨ Key Features

- **🔐 Secure Authentication:** Custom JWT integration handled via HTTP-only cookies for enhanced security against XSS.
- **🛡️ Advanced RBAC:** Granular control over user roles and permissions (Dynamic DB-driven access control).
- **📝 Post Management:** Create, edit, publish, and delete articles with rich text and media. Includes Soft Deletes (Trash functionality).
- **📂 Nested Categories:** Infinite depth category trees for organizing posts efficiently.
- **📅 Jalali Date Validation:** Native support and validation for the Persian calendar (`ValidJalaliDate` rule).
- **👥 User Dashboard:** Dedicated panel for users to manage their own posts and profile.
- **🛠️ Admin Panel:** Centralized dashboard for administrators to oversee users, roles, categories, and site content.

## 🧰 Tech Stack

- **Backend:** Laravel (PHP)
- **Database:** MySQL / SQLite (Configurable)
- **Frontend:** Blade Templates, HTML5/CSS3, Vite for asset bundling
- **Auth:** JWT (JSON Web Tokens) Custom implementation
- **Design Pattern:** Service/Repository Patterns (Extracted logic into `app/Services`)
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
