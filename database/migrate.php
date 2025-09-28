<?php

require_once __DIR__ . '/../bootstrap.php';

// Detect if running from web or CLI
$isWeb = php_sapi_name() !== 'cli';

// Format output based on context
function outputLine($message, $type = 'info') {
    global $isWeb;

    if ($isWeb) {
        // For web context, we'll capture this in ob_start()
        echo htmlspecialchars($message) . "\n";
    } else {
        // For CLI context
        echo $message . "\n";
    }
}

// Enable query logging
$capsule->getConnection()->enableQueryLog();

// Check for command-line arguments (CLI only)
$forceReset = !$isWeb && isset($argv[1]) && $argv[1] === '--reset';

try {
    // Show database path
    outputLine("Database location: " . $dbPath);
    outputLine("");

    // Only drop tables if --reset flag is provided
    if ($forceReset) {
        outputLine("Resetting database (--reset flag provided)...");
        $capsule->schema()->dropIfExists('register_log');
        $capsule->schema()->dropIfExists('runs');
        $capsule->schema()->dropIfExists('sessions');
        $capsule->schema()->dropIfExists('settings');
        $capsule->schema()->dropIfExists('users');
        outputLine("");
    }

    // Get all migration files
    $migrations = glob(__DIR__ . '/migrations/*.php');
    sort($migrations); // Sort to ensure they run in order

    outputLine("Running migrations:");
    foreach ($migrations as $migration) {
        outputLine("- Running " . basename($migration));
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
    outputLine("");
    outputLine("Executed queries:");
    foreach ($queries as $query) {
        outputLine("- " . $query['query']);
    }

    outputLine("");
    outputLine("Migration completed successfully!", 'success');
} catch (Exception $e) {
    outputLine("Migration failed: " . $e->getMessage(), 'error');
    if (!$isWeb) {
        outputLine("Stack trace:");
        outputLine($e->getTraceAsString());
    }
}
