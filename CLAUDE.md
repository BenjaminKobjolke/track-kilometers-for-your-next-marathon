# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a PHP/JavaScript web application for tracking marathon training progress. It's a full-stack app with:
- **Backend**: PHP 8.1+ with Eloquent ORM (Illuminate/Database)
- **Frontend**: Vanilla JavaScript with modular ES6 architecture
- **Database**: SQLite
- **UI**: Bootstrap 5 with responsive design
- **Session Management**: PHP sessions with user authentication

## Essential Commands

### Database Setup & Migration
```bash
# Run all migrations (creates/resets database)
php database/migrate.php
```

### Dependencies Installation
```bash
# Install PHP dependencies
composer install

# Install Node dependencies (if needed for MCP tools)
npm install
```

### Development Server
The application runs on a standard PHP web server (Apache/Nginx with PHP 8.1+). No build process required.

## Architecture

### Backend Structure
- **MVC Pattern**: Controllers in `src/Controllers/`, Models in `src/Models/`, Views in `src/Views/`
- **Entry Points**: All public requests go through `public/index.php` (front controller pattern)
- **API Endpoints**: RESTful APIs in `public/api/` handle AJAX requests
- **Authentication**: Session-based auth with activation emails via PHPMailer
- **Database**: Eloquent ORM with migrations in `database/migrations/`

### Frontend Architecture
- **Modular JavaScript**: ES6 modules in `public/js/modules/`
- **Main Entry**: `public/js/app.js` initializes all managers
- **Key Managers**:
  - `RunManager`: Handles running data CRUD operations
  - `SessionManager`: Manages training sessions
  - `StatsManager`: Calculates statistics and probabilities
  - `TranslationManager`: Multi-language support (en/de)
  - `AuthManager`: Frontend authentication handling
  - `UIManager`: DOM manipulation and UI updates

### Database Schema
- **users**: User accounts with email authentication
- **sessions**: Training periods with start/end dates and target kilometers
- **runs**: Individual run records linked to sessions
- **settings**: Global application settings including language

### Key Flows
1. **Authentication**: Login → Session creation → Redirect to main app
2. **Session Management**: Users can have multiple training sessions (active/completed)
3. **Run Tracking**: Runs belong to sessions, automatically calculate stats
4. **Multi-language**: Settings stored in DB, translations in `public/data/lang/`

## Configuration

### Required Config Files
1. **config.php**: Copy from `config_example.php` and set:
   - `base_url`: Application base URL path
   - `email.from_address`: Sender email for password resets
   - `email.from_name`: Display name for emails

2. **.htaccess**: Already configured for URL rewriting (public directory)

### Environment Setup
- PHP 8.1+ with SQLite extension
- Write permissions for `database/` directory
- Write permissions for `logs/` directory
- SMTP configuration for email features (password reset)

## Important Patterns

### API Response Format
All API endpoints return JSON with consistent structure:
```json
{
  "success": true/false,
  "data": {...} or "message": "error message"
}
```

### Error Handling
- Backend logs to `logs/app.log` and `logs/auth.log`
- Frontend shows user-friendly translated error messages
- All database operations use try-catch blocks

### Session State
- Active session ID stored in PHP session (`$_SESSION['active_session_id']`)
- User ID stored in `$_SESSION['user_id']`
- Frontend fetches session data via API calls

### Translation System
- Backend: `Models\TranslationManager` reads from JSON files
- Frontend: `modules/TranslationManager.js` handles client-side translations
- Language preference stored in database settings

## Development Notes

- No build/compilation step needed - direct PHP/JS execution
- Bootstrap CSS included via CDN in header.php
- All dates use ISO format (YYYY-MM-DD) for consistency
- Probability calculations use Monte Carlo simulation in frontend
- Email functionality requires proper SMTP configuration