# PHP Starter Pack

A lightweight and easy-to-use PHP starter pack for quickly bootstrapping web projects. This pack includes a basic admin login system and is built with raw PHP, a touch of JavaScript, and styled beautifully with Tailwind CSS CDN.

---

## ✨ Features

- **Admin Login System**: Secure and straightforward authentication for administrator access.
- **Raw PHP**: No complex frameworks or dependencies, just pure PHP for maximum control and understanding.
- **Simple JavaScript Integration**: Enhance user experience with minimal, un-bundled JavaScript.
- **Tailwind CSS CDN**: Rapidly style your application with utility-first CSS without a build process.
- **Modular Structure**: Organized codebase for easy navigation and expansion.

---

## 🛠️ Technologies Used

- **PHP**: Core backend logic.
- **JavaScript**: Minor frontend interactivity.
- **Tailwind CSS (CDN)**: For modern and responsive styling.
- **HTML5**: Structure of the web pages.

---

## 🚀 Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/emonv2/emon-php.git
cd emon-php
```

### 2.Web Server Configuration

- Place the project files in your web server's document root (e.g., htdocs for Apache, www for Nginx).
- Ensure your web server (Apache, Nginx, XAMPP, WAMP, etc.) is configured to serve PHP files.

### 3. Database Setup (MySQL/MariaDB)

- Create a new database (e.g., php_starter_db).
- Import the database.sql file into your newly created database.
  This file will contain the necessary tables, including the users table for the admin login system.

### 4. Configuration

- Open lib/database.php and update the database credentials:

```php
// Your mysql host
private $hostdb = "localhost";
// Your mysql username
private $userdb = "root";
// Your mysql password
private $passdb = "eatuany";
// Your mysql bd name
private $namedb = "ads_server";
```

- Open lib/config.php and update the site configs:

```php
// If your apps run on a sub folder then enter the subfolder name here or if not just leave it empty
$sub_folder_name = 'emon-php';
// This is your website name
$site_name = 'Emon Starter';
// This is the warning massage if you try to delete any data
$delete_warning = 'Are you sure you want to delete? If you do not, click cancel.';
// This for pagination, How many items are show that determine that
$item_per_page = 6;
// This is a secrete key for encryption your data
$app_secret_key = "kj9dJjd76I27U8HklK3jLsg4js8374";
```
