# Technical Context

## Technologies Used

1. Frontend

   - HTML5/CSS3
   - JavaScript (ES6+)
   - Bootstrap 5
   - Fetch API

2. Backend

   - PHP 8+
   - SQLite
   - Eloquent ORM
   - PHPMailer

3. Development Tools
   - Composer for PHP dependencies
   - Git for version control
   - VSCode editor
   - PHP-CS-Fixer for formatting

## Development Setup

1. Prerequisites

   - PHP 8+ installed
   - Composer installed
   - Web server (Apache/Nginx)
   - SQLite support enabled

2. Installation Steps

   ```bash
   # Install PHP dependencies
   composer install

   # Create SQLite database
   touch database/database.sqlite
   chmod 777 database/database.sqlite

   # Run migrations
   php database/migrate.php
   ```

3. Configuration
   ```php
   // config.php
   return [
       'base_url' => '/track-kilometers-for-your-next-marathon/public',
       'email' => [
           'from_address' => 'marathon-tracker@your-server.com',
           'from_name' => 'Marathon Training Tracker'
       ]
   ];
   ```

## Technical Constraints

1. Database

   - SQLite for portability
   - File permissions (777)
   - Foreign key constraints
   - Index optimizations

2. Authentication

   - Session-based auth
   - Token-based reset
   - Remember me cookies
   - CSRF protection

3. Email
   - PHP mail() function
   - HTML email support
   - Token expiration
   - Error handling

## Dependencies

1. PHP Packages

   ```json
   {
     "illuminate/database": "^8.0",
     "phpmailer/phpmailer": "^6.0"
   }
   ```

2. Frontend Libraries

   ```html
   <link href="bootstrap@5.3.0/css/bootstrap.min.css" rel="stylesheet" />
   <script src="bootstrap@5.3.0/js/bootstrap.bundle.min.js"></script>
   ```

3. Development Tools
   - PHP-CS-Fixer
   - Git
   - Composer
   - VSCode

## Tool Usage Patterns

1. Database Operations

   ```php
   // Using Eloquent ORM
   $runs = Run::orderBy('date', 'desc')->get();
   $settings = Settings::getDefault();
   ```

2. API Endpoints

   ```php
   // RESTful pattern
   header('Content-Type: application/json');
   echo json_encode(['success' => true]);
   ```

3. Frontend Modules

   ```javascript
   // ES6 modules
   import { RunManager } from "./modules/RunManager.js";
   import { SettingsManager } from "./modules/SettingsManager.js";
   ```

4. Configuration Usage

   ```javascript
   // JavaScript
   fetch(`${window.appConfig.baseUrl}/api/auth/login.php`);

   // PHP
   $config = require_once __DIR__ . '/config.php';
   $resetLink = 'http://' . $_SERVER['HTTP_HOST'] . $config['base_url'];
   ```
