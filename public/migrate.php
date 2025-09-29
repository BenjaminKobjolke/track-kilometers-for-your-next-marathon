<?php
// Start session with specific namespace to avoid conflicts
session_start();
$config = require __DIR__ . '/../config.php';

// Use a separate session variable for migration tool to avoid conflicts with main app
$isAuthenticated = isset($_SESSION['migration_auth']) && $_SESSION['migration_auth'] === true;

// Check if session has expired (30 minutes timeout)
if ($isAuthenticated && isset($_SESSION['migration_auth_time'])) {
    if (time() - $_SESSION['migration_auth_time'] > 1800) { // 30 minutes
        unset($_SESSION['migration_auth']);
        unset($_SESSION['migration_auth_time']);
        $isAuthenticated = false;
        $error = 'Session expired. Please login again.';
    } else {
        $_SESSION['migration_auth_time'] = time(); // Update activity time
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['migration_auth']);
    unset($_SESSION['migration_auth_time']);
    header('Location: migrate.php');
    exit;
}

// Handle authentication
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === $config['migration']['password']) {
        $_SESSION['migration_auth'] = true;
        $_SESSION['migration_auth_time'] = time(); // Set initial auth time
        header('Location: migrate.php');
        exit;
    } else {
        $error = 'Invalid password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Migration Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .terminal-output {
            background-color: #1e1e1e;
            color: #d4d4d4;
            font-family: 'Courier New', monospace;
            padding: 15px;
            border-radius: 5px;
            max-height: 500px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .success-line { color: #4ec9b0; }
        .error-line { color: #f48771; }
        .info-line { color: #9cdcfe; }
        .warning-line { color: #dcdcaa; }
        .backup-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .backup-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php if (!$isAuthenticated): ?>
            <!-- Login Form -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Migration Tool - Authentication Required</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Migration Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required autofocus>
                                    <small class="text-muted">Password is configured in config.php</small>
                                </div>
                                <button type="submit" class="btn btn-primary">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Migration Interface -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Database Migration Tool</h1>
                <a href="?logout=1" class="btn btn-sm btn-outline-secondary">Logout</a>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <!-- Migration Controls -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Migration Controls</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <strong>Database:</strong> <?= htmlspecialchars($config['database']['path']) ?>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-primary w-100" id="runMigration" onclick="runMigration('incremental')">
                                        Run Incremental Migration
                                    </button>
                                    <small class="text-muted d-block mt-1">Only applies missing migrations</small>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-danger w-100" id="resetMigration" onclick="confirmReset()">
                                        Reset & Rebuild Database
                                    </button>
                                    <small class="text-muted d-block mt-1">Drops all tables and rebuilds from scratch</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Output -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Migration Output</h5>
                        </div>
                        <div class="card-body">
                            <div id="output" class="terminal-output">Ready to run migrations...</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Backup History -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Backup History</h5>
                        </div>
                        <div class="card-body">
                            <div id="backupList">
                                <div class="text-muted">Loading backups...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Migration Files -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Migration Files</h5>
                        </div>
                        <div class="card-body">
                            <div id="migrationFiles">
                                <div class="text-muted">Loading migration files...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php if ($isAuthenticated): ?>
    <script>
        const baseUrl = '<?= $config['base_url'] ?>';

        // Load initial data
        document.addEventListener('DOMContentLoaded', function() {
            loadBackups();
            loadMigrationFiles();
        });

        function loadBackups() {
            fetch(`${baseUrl}/api/migrate.php?action=list_backups`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `password=${encodeURIComponent('<?= $config['migration']['password'] ?>')}`
            })
            .then(response => response.json())
            .then(data => {
                const backupList = document.getElementById('backupList');
                if (data.success && data.backups && data.backups.length > 0) {
                    backupList.innerHTML = data.backups.map(backup => `
                        <div class="backup-item">
                            <div>
                                <small class="d-block">${backup.name}</small>
                                <small class="text-muted">${backup.size} - ${backup.date}</small>
                            </div>
                        </div>
                    `).join('');
                } else {
                    backupList.innerHTML = '<div class="text-muted">No backups found</div>';
                }
            })
            .catch(error => {
                document.getElementById('backupList').innerHTML = '<div class="text-danger">Failed to load backups</div>';
            });
        }

        function loadMigrationFiles() {
            fetch(`${baseUrl}/api/migrate.php?action=list_migrations`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `password=${encodeURIComponent('<?= $config['migration']['password'] ?>')}`
            })
            .then(response => response.json())
            .then(data => {
                const fileList = document.getElementById('migrationFiles');
                if (data.success && data.migrations && data.migrations.length > 0) {
                    fileList.innerHTML = data.migrations.map(file => `
                        <div class="small mb-1">${file}</div>
                    `).join('');
                } else {
                    fileList.innerHTML = '<div class="text-muted">No migration files found</div>';
                }
            })
            .catch(error => {
                document.getElementById('migrationFiles').innerHTML = '<div class="text-danger">Failed to load files</div>';
            });
        }

        function runMigration(type) {
            const output = document.getElementById('output');
            output.innerHTML = '<span class="info-line">Starting migration...</span>\n';

            // Disable buttons
            document.getElementById('runMigration').disabled = true;
            document.getElementById('resetMigration').disabled = true;

            fetch(`${baseUrl}/api/migrate.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `password=${encodeURIComponent('<?= $config['migration']['password'] ?>')}&type=${type}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    output.innerHTML = formatOutput(data.output);
                    if (data.backup) {
                        output.innerHTML += `\n<span class="success-line">✓ Backup created: ${data.backup}</span>`;
                    }
                    output.innerHTML += `\n<span class="success-line">✓ Migration completed successfully!</span>`;
                    // Reload backups
                    loadBackups();
                } else {
                    output.innerHTML = `<span class="error-line">✗ Migration failed: ${data.message || 'Unknown error'}</span>`;
                    if (data.details) {
                        output.innerHTML += `\n<span class="error-line">${data.details}</span>`;
                    }
                }
            })
            .catch(error => {
                output.innerHTML = `<span class="error-line">✗ Request failed: ${error.message}</span>`;
            })
            .finally(() => {
                // Re-enable buttons
                document.getElementById('runMigration').disabled = false;
                document.getElementById('resetMigration').disabled = false;
                output.scrollTop = output.scrollHeight;
            });
        }

        function confirmReset() {
            if (confirm('WARNING: This will DELETE all data and rebuild the database from scratch. Are you sure?')) {
                if (confirm('This action cannot be undone (though a backup will be created). Continue?')) {
                    runMigration('reset');
                }
            }
        }

        function formatOutput(text) {
            if (!text) return '';

            return text.split('\n').map(line => {
                if (line.includes('✓') || line.includes('success') || line.includes('completed')) {
                    return `<span class="success-line">${escapeHtml(line)}</span>`;
                } else if (line.includes('✗') || line.includes('error') || line.includes('failed')) {
                    return `<span class="error-line">${escapeHtml(line)}</span>`;
                } else if (line.includes('Running') || line.includes('Creating')) {
                    return `<span class="info-line">${escapeHtml(line)}</span>`;
                } else if (line.includes('Warning')) {
                    return `<span class="warning-line">${escapeHtml(line)}</span>`;
                } else {
                    return escapeHtml(line);
                }
            }).join('\n');
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    </script>
    <?php endif; ?>
</body>
</html>