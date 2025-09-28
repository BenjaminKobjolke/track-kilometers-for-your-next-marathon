<?php

require_once __DIR__ . '/../bootstrap.php';

// Enable query logging
$capsule->getConnection()->enableQueryLog();

// Check for command-line arguments
$forceReset = isset($argv[1]) && $argv[1] === '--reset';

try {
    // Show database path
    echo "Database location: " . $dbPath . "\n\n";

    // Only drop tables if --reset flag is provided
    if ($forceReset) {
        echo "Resetting database (--reset flag provided)...\n";
        $capsule->schema()->dropIfExists('register_log');
        $capsule->schema()->dropIfExists('runs');
        $capsule->schema()->dropIfExists('sessions');
        $capsule->schema()->dropIfExists('settings');
        $capsule->schema()->dropIfExists('users');
        echo "\n";
    }

    // Get all migration files
    $migrations = glob(__DIR__ . '/migrations/*.php');
    sort($migrations); // Sort to ensure they run in order

    echo "Running migrations:\n";
    foreach ($migrations as $migration) {
        echo "- Running " . basename($migration) . "\n";
        $migrationData = require $migration;
        
        if (isset($migrationData['up']) && is_callable($migrationData['up'])) {
            $migrationData['up']();
        } else {
            // For old-style migrations that don't use up/down functions
            require $migration;
        }
    }

    // Output executed queries
    $queries = $capsule->getConnection()->getQueryLog();
    echo "\nExecuted queries:\n";
    foreach ($queries as $query) {
        echo "- " . $query['query'] . "\n";
    }

    echo "\nMigration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
