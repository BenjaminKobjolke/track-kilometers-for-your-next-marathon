<?php

// show errors in development
error_reporting(E_ALL);
ini_set('display_errors', '1');

require 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Models\Logger;

// Register autoloading for all src/ namespaces
spl_autoload_register(function ($class) {
    // List of namespaces mapped to directories
    $namespaces = [
        'Models\\' => __DIR__ . '/src/Models/',
        'Controllers\\' => __DIR__ . '/src/Controllers/',
        'Utils\\' => __DIR__ . '/src/Utils/',
    ];
    
    foreach ($namespaces as $namespace => $base_dir) {
        if (strpos($class, $namespace) === 0) {
            $relative_class = substr($class, strlen($namespace));
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
            
            // Debug logging for deployment troubleshooting
            if (isset($GLOBALS['debug_autoload'])) {
                error_log("Attempting to load class: $class from file: $file");
                error_log("File exists: " . (file_exists($file) ? 'YES' : 'NO'));
                error_log("Directory exists: " . (is_dir($base_dir) ? 'YES' : 'NO'));
            }
            
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

// Initialize logger
$logger = new Logger();

// Define database path
$dbPath = __DIR__ . '/database/database.sqlite';

// Check if database exists
if (!file_exists($dbPath)) {
    $logger->info('Database file not found, creating', ['path' => $dbPath]);
    
    // Create database directory if it doesn't exist
    $dbDir = dirname($dbPath);
    if (!file_exists($dbDir)) {
        $logger->info('Creating database directory', ['path' => $dbDir]);
        if (!mkdir($dbDir, 0777, true)) {
            $logger->error('Failed to create database directory');
            throw new Exception('Failed to create database directory');
        }
    }
    
    // Create empty database file
    $logger->info('Creating database file');
    if (!touch($dbPath)) {
        $logger->error('Failed to create database file');
        throw new Exception('Failed to create database file');
    }
    
    if (!chmod($dbPath, 0777)) {
        $logger->error('Failed to set database file permissions');
        throw new Exception('Failed to set database file permissions');
    }
    
    $logger->info('Database file created successfully');
}

// Check database permissions
if (!is_writable($dbPath)) {
    $logger->error('Database file not writable', [
        'path' => $dbPath,
        'permissions' => decoct(fileperms($dbPath) & 0777)
    ]);
    throw new Exception('Database file not writable');
}

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'sqlite',
    'database'  => $dbPath,
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Enable foreign keys for SQLite
$capsule->getConnection()->statement('PRAGMA foreign_keys = ON;');

// Run migrations if database was just created
if (filesize($dbPath) === 0) {
    $logger->info('Running initial database migrations');
    try {
        require_once __DIR__ . '/database/migrate.php';
        $logger->info('Migrations completed successfully');
    } catch (Exception $e) {
        $logger->error('Migration failed', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}
