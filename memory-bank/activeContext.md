# Active Context

## Current Work Focus

1. Base URL Configuration

   - Created config.php for environment settings
   - Added baseUrl configuration
   - Updated all JavaScript and PHP paths
   - Fixed password reset email links

2. Authentication System

   - Login/logout functionality
   - Password reset system
   - Remember me feature
   - Session management

3. Error Logging
   - Custom Logger class
   - Separate log files per type
   - Detailed error context
   - File permission handling

## Recent Changes

1. Configuration Updates

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

2. JavaScript Updates

   ```javascript
   // Using baseUrl in fetch calls
   fetch(`${window.appConfig.baseUrl}/api/auth/login.php`);
   window.location.href = `${window.appConfig.baseUrl}/login.php`;
   ```

3. PHP Updates
   ```php
   // Using config in reset emails
   $resetLink = 'http://' . $_SERVER['HTTP_HOST'] . $config['base_url'];
   $mail->setFrom($config['email']['from_address'], $config['email']['from_name']);
   ```

## Active Decisions

1. Configuration Management

   - Single config.php file
   - Environment-specific settings
   - Passed to JavaScript via window.appConfig
   - Used consistently across all paths

2. Error Handling

   - Custom Logger class
   - File-based logging
   - Detailed context in logs
   - Separate log files by type

3. Path Structure
   - Base URL in config
   - Consistent usage in JavaScript
   - Consistent usage in PHP
   - Email link generation

## Important Patterns

1. Configuration Usage

   - Load in PHP: require_once config.php
   - Pass to JS: window.appConfig
   - Use in fetch calls
   - Use in redirects

2. Error Logging

   - Create log directory
   - Set permissions
   - Log with context
   - Handle file errors

3. Path Management
   - Config-based paths
   - Consistent structure
   - Environment flexibility
   - Email compatibility

## Project Insights

1. Configuration

   - Single source of truth
   - Easy environment changes
   - Consistent usage pattern
   - Flexible deployment

2. Error Handling

   - Detailed logging
   - Easy debugging
   - File management
   - Permission handling

3. Path Structure
   - Environment agnostic
   - Easy to maintain
   - Consistent format
   - Email compatibility
