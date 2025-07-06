# System Patterns

## Architecture Overview

1. Frontend Architecture

   - Modular JavaScript using ES6 modules
   - Bootstrap for responsive UI
   - Event-driven interactions
   - Client-side form validation

2. Backend Architecture
   - PHP RESTful API endpoints
   - Eloquent ORM for database
   - Session-based authentication
   - Token-based password reset

## Design Patterns

1. Module Pattern

   - AuthManager for authentication
   - RunManager for run operations
   - SettingsManager for preferences
   - DateFormatter for date handling

2. MVC Pattern

   - Models: User, Run, Settings
   - Views: PHP templates
   - Controllers: API endpoints

3. Observer Pattern
   - Event listeners for UI interactions
   - Form submission handlers
   - Modal management

## Implementation Paths

1. Authentication Flow

   ```
   Login -> Validate -> Create Session -> Redirect
   Password Reset -> Generate Token -> Send Email -> Validate Token -> Update
   ```

2. Run Management Flow

   ```
   Add Run -> Validate -> Save -> Update Stats
   Edit Run -> Validate -> Update -> Recalculate Stats
   Delete Run -> Confirm -> Remove -> Update Stats
   ```

3. Settings Flow
   ```
   Update Settings -> Validate -> Save -> Refresh UI
   Theme Change -> Save -> Apply CSS
   ```

## Component Relationships

1. Authentication Components

   - AuthManager <-> Login API
   - AuthManager <-> Reset API
   - Session <-> User Model

2. Run Components

   - RunManager <-> Runs API
   - Run Model <-> Database
   - Stats <-> Run Collection

3. Settings Components
   - SettingsManager <-> Settings API
   - Settings Model <-> Database
   - Theme <-> CSS Classes

## Configuration System

1. Base URL Management

   - config.php for environment settings
   - Passed to JavaScript via window.appConfig
   - Used consistently across:
     - API endpoints
     - Page redirects
     - Password reset emails

2. Database Configuration

   - SQLite connection settings
   - Foreign key constraints
   - Migrations for schema changes

3. Email Configuration
   - SMTP settings
   - Email templates
   - Token generation/validation

## Critical Paths

1. User Authentication

   - Login validation
   - Session management
   - Password security
   - Token handling

2. Data Integrity

   - Input validation
   - Database constraints
   - Error handling
   - Transaction management

3. User Experience
   - Form validation
   - Error messages
   - Loading states
   - Responsive design
