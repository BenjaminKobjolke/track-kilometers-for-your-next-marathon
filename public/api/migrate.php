<?php
require_once __DIR__ . '/../../bootstrap.php';

$config = require __DIR__ . '/../../config.php';

// Set JSON header
header('Content-Type: application/json');

// Verify password
if (!isset($_POST['password']) || $_POST['password'] !== $config['migration']['password']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid password']);
    exit;
}

// Handle different actions
$action = $_GET['action'] ?? 'migrate';

try {
    switch ($action) {
        case 'list_backups':
            $backupDir = __DIR__ . '/../../data/backups';
            $backups = [];

            if (is_dir($backupDir)) {
                $files = glob($backupDir . '/database_*.sqlite');
                rsort($files); // Newest first

                foreach (array_slice($files, 0, 10) as $file) { // Show last 10
                    $backups[] = [
                        'name' => basename($file),
                        'size' => formatBytes(filesize($file)),
                        'date' => date('Y-m-d H:i:s', filemtime($file))
                    ];
                }
            }

            echo json_encode(['success' => true, 'backups' => $backups]);
            break;

        case 'list_migrations':
            $migrationDir = __DIR__ . '/../../database/migrations';
            $migrations = [];

            if (is_dir($migrationDir)) {
                $files = glob($migrationDir . '/*.php');
                sort($files);

                foreach ($files as $file) {
                    $migrations[] = basename($file);
                }
            }

            echo json_encode(['success' => true, 'migrations' => $migrations]);
            break;

        case 'migrate':
        default:
            $type = $_POST['type'] ?? 'incremental';

            // Create backup first
            $backupFile = createBackup($config['database']['path']);

            // Run migration
            ob_start();
            $result = runMigration($type === 'reset');
            $output = ob_get_clean();

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'output' => $output,
                    'backup' => basename($backupFile)
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Migration failed',
                    'details' => $output
                ]);
            }
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}

function createBackup($dbPath) {
    // Create backup directory if it doesn't exist
    $backupDir = dirname($dbPath) . '/backups';
    if (!is_dir($backupDir)) {
        if (!mkdir($backupDir, 0777, true)) {
            throw new Exception('Failed to create backup directory');
        }
    }

    // Generate backup filename with timestamp
    $timestamp = date('Ymd_His');
    $backupFile = $backupDir . '/database_' . $timestamp . '.sqlite';

    // Copy database file
    if (!copy($dbPath, $backupFile)) {
        throw new Exception('Failed to create database backup');
    }

    // Clean old backups (keep last 10)
    $backupFiles = glob($backupDir . '/database_*.sqlite');
    if (count($backupFiles) > 10) {
        rsort($backupFiles);
        $filesToDelete = array_slice($backupFiles, 10);
        foreach ($filesToDelete as $file) {
            unlink($file);
        }
    }

    return $backupFile;
}

function runMigration($reset = false) {
    global $capsule;

    // Enable query logging
    $capsule->getConnection()->enableQueryLog();

    try {
        $dbPath = $capsule->getConnection()->getConfig('database');
        echo "Database location: " . $dbPath . "\n\n";

        // Only drop tables if reset is requested
        if ($reset) {
            echo "Resetting database...\n";
            $capsule->schema()->dropIfExists('register_log');
            $capsule->schema()->dropIfExists('runs');
            $capsule->schema()->dropIfExists('sessions');
            $capsule->schema()->dropIfExists('settings');
            $capsule->schema()->dropIfExists('users');
            echo "All tables dropped.\n\n";
        }

        // Get all migration files
        $migrations = glob(__DIR__ . '/../../database/migrations/*.php');
        sort($migrations); // Sort to ensure they run in order

        echo "Running migrations:\n";
        foreach ($migrations as $migration) {
            echo "- Running " . basename($migration) . "\n";
            $migrationData = require $migration;

            if (isset($migrationData['up']) && is_callable($migrationData['up'])) {
                $migrationData['up']();
            }
        }

        echo "\nMigration completed successfully!\n";

        // Output query count
        $queries = $capsule->getConnection()->getQueryLog();
        echo "Executed " . count($queries) . " queries.\n";

        return true;
    } catch (Exception $e) {
        echo "\nMigration failed: " . $e->getMessage() . "\n";
        return false;
    }
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}