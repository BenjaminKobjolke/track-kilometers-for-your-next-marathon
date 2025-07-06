<?php

require_once __DIR__ . '/../bootstrap.php';
$config = require_once __DIR__ . '/../config.php';

use App\Models\Run;
use App\Models\Settings;
use App\Models\User;

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Check remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        $user = User::where('remember_token', $_COOKIE['remember_token'])->first();
        if ($user && $user->isRememberTokenValid()) {
            $_SESSION['user_id'] = $user->id;
        } else {
            header('Location: login.php');
            exit;
        }
    } else {
        header('Location: login.php');
        exit;
    }
}

$settings = Settings::getDefault();
$runs = Run::orderBy('date', 'desc')->get();

$totalKilometers = Run::sum('kilometers');
$averageKilometers = Run::calculateAverageKilometers($settings->start_date, date('Y-m-d'));
$estimatedTotal = $settings->getEstimatedTotalKilometers();
$remainingDays = $settings->getRemainingDays();
$probability = $settings->getTargetProbability();

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marathon Training Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body data-theme="<?= $settings->theme ?>">
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 col-7">
                <h1>Marathon Training Tracker</h1>
            </div>
            <div class="col-md-4 col-5 text-end">
                <div class="d-flex flex-md-row flex-column justify-content-end">
                    <button type="button" class="btn btn-success mb-md-0 mb-2 me-md-2" data-bs-toggle="modal" data-bs-target="#addRunModal">
                        Add Run
                    </button>
                    <button type="button" class="btn btn-primary mb-md-0 mb-2 me-md-2" data-bs-toggle="modal" data-bs-target="#settingsModal">
                        Settings
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="logoutButton">
                        Logout
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Panel -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="card-info">
                                <h5 class="card-title">Total Kilometers</h5>
                                <p class="card-subtext">since <?= $settings->formatted_start_date ?></p>
                            </div>
                            <p class="card-text"><?= number_format($totalKilometers, 1) ?> km</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="card-info">
                                <h5 class="card-title">Estimated Total</h5>
                                <p class="card-subtext">daily average of <?= number_format($averageKilometers, 1) ?>&nbsp;km</p>
                            </div>
                            <p class="card-text"><?= number_format($estimatedTotal, 1) ?> km</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="card-info">
                                <h5 class="card-title">Remaining Days</h5>
                                <p class="card-subtext">until <?= $settings->formatted_end_date ?></p>
                            </div>
                            <p class="card-text"><?= $remainingDays ?> days</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="card-info">
                                <h5 class="card-title">Target Probability</h5>
                                <p class="card-subtext">Target of <?= number_format($settings->target_kilometers, 1) ?>&nbsp;km</p>
                            </div>
                            <p class="card-text"><?= number_format($probability, 1) ?>%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Runs Table -->
        <div class="table-responsive mt-4">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Kilometers</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($runs as $run): ?>
                    <tr>
                        <td><?= $run->formatted_date ?></td>
                        <td><?= number_format($run->kilometers, 1) ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-run me-2" 
                                    data-id="<?= $run->id ?>"
                                    data-date="<?= $run->formatted_date ?>"
                                    data-km="<?= $run->kilometers ?>">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger delete-run"
                                    data-id="<?= $run->id ?>"
                                    data-date="<?= $run->formatted_date ?>">
                                Delete
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Settings Modal -->
    <div class="modal fade" id="settingsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="settingsForm">
                        <div class="mb-3">
                            <label for="startDate" class="form-label">Start Date</label>
                            <input type="text" class="form-control" id="startDate" name="start_date" 
                                   value="<?= $settings->formatted_start_date ?>"
                                   placeholder="DD.MM.YYYY">
                        </div>
                        <div class="mb-3">
                            <label for="endDate" class="form-label">End Date</label>
                            <input type="text" class="form-control" id="endDate" name="end_date" 
                                   value="<?= $settings->formatted_end_date ?>"
                                   placeholder="DD.MM.YYYY">
                        </div>
                        <div class="mb-3">
                            <label for="targetKm" class="form-label">Target Kilometers</label>
                            <input type="number" step="0.1" class="form-control" id="targetKm" name="target_kilometers" 
                                   value="<?= $settings->target_kilometers ?>">
                        </div>
                        <div class="mb-3">
                            <label for="theme" class="form-label">Theme</label>
                            <select class="form-control" id="theme" name="theme">
                                <option value="light" <?= $settings->theme === 'light' ? 'selected' : '' ?>>Light</option>
                                <option value="dark" <?= $settings->theme === 'dark' ? 'selected' : '' ?>>Dark</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveSettings">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Run Modal -->
    <div class="modal fade" id="addRunModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Run</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="runForm">
                        <input type="hidden" id="runId">
                        <div class="mb-3">
                            <label for="runDate" class="form-label">Date</label>
                            <input type="text" class="form-control" id="runDate" name="date" required placeholder="DD.MM.YYYY">
                        </div>
                        <div class="mb-3">
                            <label for="kilometers" class="form-label">Kilometers</label>
                            <input type="number" step="0.1" class="form-control" id="kilometers" name="kilometers" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveRun">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.appConfig = {
            baseUrl: '<?= $config['base_url'] ?>'
        };
    </script>
    <script type="module" src="js/app.js"></script>
</body>
</html>
