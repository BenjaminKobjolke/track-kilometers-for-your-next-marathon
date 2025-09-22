<?php

require_once __DIR__ . '/../bootstrap.php';

// Enable query logging
$capsule->getConnection()->enableQueryLog();

try {
    // Show database path
    echo "Database location: " . $dbPath . "\n\n";

    // Drop all existing tables first
    $capsule->schema()->dropIfExists('runs');
    $capsule->schema()->dropIfExists('settings');
    $capsule->schema()->dropIfExists('users');
    $capsule->schema()->dropIfExists('sessions');

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
