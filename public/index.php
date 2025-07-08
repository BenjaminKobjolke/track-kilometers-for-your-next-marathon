<?php

require_once __DIR__ . '/../bootstrap.php';
$config = require_once __DIR__ . '/../config.php';

use Models\Run;
use Models\Settings;
use Models\User;
use Models\Session;
use Models\DateFormatter;

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

// Get active session
$activeSession = null;
if (isset($_SESSION['active_session_id'])) {
    $activeSession = Session::where('id', $_SESSION['active_session_id'])
        ->where('user_id', $_SESSION['user_id'])
        ->where('status', '=', 'active')
        ->first();
    
    // Clear session ID if session is not found or not active
    if (!$activeSession) {
        unset($_SESSION['active_session_id']);
    }
}

// If no active session, try to find the most recent active session
if (!$activeSession) {
    $activeSession = Session::where('user_id', $_SESSION['user_id'])
        ->where('status', '=', 'active')
        ->orderBy('created_at', 'desc')
        ->first();
    
    // Store the session ID if found
    if ($activeSession) {
        $_SESSION['active_session_id'] = $activeSession->id;
    }
}

// If no active session, we'll show empty stats
$runs = [];
$totalKilometers = 0;
$averageKilometers = 0;
$estimatedTotal = 0;
$remainingDays = 0;
$probability = 0;

if ($activeSession && $activeSession->status === 'active') {
    $runs = Run::where('session_id', $activeSession->id)
        ->orderBy('date', 'desc')
        ->get();
    
    $totalKilometers = $runs->sum('kilometers');
    $averageKilometers = Run::calculateAverageKilometers($activeSession->start_date, $activeSession->end_date, $activeSession->id);
    
    // Calculate estimated total based on current average
    $totalDays = ceil((strtotime($activeSession->end_date) - strtotime($activeSession->start_date)) / (60 * 60 * 24)) + 1;
    $estimatedTotal = $averageKilometers * $totalDays;
    
    // Calculate remaining days
    $remainingDays = ceil((strtotime($activeSession->end_date) - time()) / (60 * 60 * 24));
    $remainingDays = max(0, $remainingDays);
    
    // Calculate probability (simplified version)
    $probability = min(100, ($totalKilometers / $activeSession->target_kilometers) * 100);
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Training Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body data-theme="<?= $settings->theme ?>">
    <div class="container mt-4">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid px-0">
                <div>
                    <h1 class="navbar-brand mb-0">Training Tracker</h1>
                    <?php if ($activeSession): ?>
                    <div class="navbar-text" id="currentSessionInfo">
                        Session: <?= htmlspecialchars($activeSession->name) ?>
                    </div>
                    <?php endif; ?>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarContent">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <button class="btn btn-success nav-link" data-bs-toggle="modal" data-bs-target="#addRunModal" <?php echo !$activeSession ? 'disabled' : ''; ?>>
                                Add Entry
                            </button>
                        </li>
                        <li class="nav-item dropdown">
                            <button class="btn nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                Session
                            </button>
                            <ul class="dropdown-menu">
                                <?php if ($activeSession): ?>
                                <li>
                                    <button class="dropdown-item" id="completeSession">
                                        Complete Current Session
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li>
                                    <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#createSessionModal">
                                        New Session
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item" id="switchSession">
                                        Switch Session
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#pastSessionsModal">
                                        Past Sessions
                                    </button>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link" data-bs-toggle="modal" data-bs-target="#settingsModal">
                                Settings
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn nav-link" id="logoutButton">
                                Logout
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Statistics Panel -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="card-info">
                                <h5 class="card-title">Total Kilometers</h5>
                                <?php if ($activeSession): ?>
                                <p class="card-subtext">since <?= DateFormatter::isoToGermanDate($activeSession->start_date) ?></p>
                                <?php endif; ?>
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
                                <?php if ($activeSession): ?>
                                <p class="card-subtext">until <?= DateFormatter::isoToGermanDate($activeSession->end_date) ?></p>
                                <?php endif; ?>
                            </div>
                            <p class="card-text"><?= $remainingDays ?> days</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card probability-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="card-info">
                                <h5 class="card-title">Target Probability</h5>
                                <p class="card-subtext">Target of <?= number_format($activeSession ? $activeSession->target_kilometers : 0, 1) ?>&nbsp;km</p>
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

    <!-- Create Session Modal -->
    <div class="modal fade" id="createSessionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Session</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createSessionForm">
                        <div class="mb-3">
                            <label for="sessionName" class="form-label">Session Name</label>
                            <input type="text" class="form-control" id="sessionName" required>
                        </div>
                        <div class="mb-3">
                            <label for="sessionStartDate" class="form-label">Start Date</label>
                            <input type="text" class="form-control" id="sessionStartDate" data-type="date" required placeholder="DD.MM.YYYY">
                        </div>
                        <div class="mb-3">
                            <label for="sessionEndDate" class="form-label">End Date</label>
                            <input type="text" class="form-control" id="sessionEndDate" data-type="date" required placeholder="DD.MM.YYYY">
                        </div>
                        <div class="mb-3">
                            <label for="sessionTargetKm" class="form-label">Target Kilometers</label>
                            <input type="number" step="0.1" class="form-control" id="sessionTargetKm" required value="500">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="createSessionForm">Create</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Select Session Modal -->
    <div class="modal fade" id="selectSessionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Session</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="sessionList">
                        <!-- Sessions will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Past Sessions Modal -->
    <div class="modal fade" id="pastSessionsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Past Sessions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Period</th>
                                    <th>Total KM</th>
                                    <th>Daily Average</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="pastSessionsList">
                                <!-- Past sessions will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Probability Info Modal -->
    <div class="modal fade" id="probabilityInfoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Target Probability Calculation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>The target probability is calculated using a weighted combination of your current progress and estimated progress:</p>
                    
                    <div class="mb-3">
                        <h6>Current Progress (30% weight)</h6>
                        <p>How far you've come: <span id="currentProgressValue"></span>%<br>
                        (<span id="currentKmValue"></span> km of <span id="targetKmValue"></span> km)</p>
                    </div>

                    <div class="mb-3">
                        <h6>Estimated Progress (70% weight)</h6>
                        <p>Where you're projected to be: <span id="estimatedProgressValue"></span>%<br>
                        (Estimated <span id="estimatedKmValue"></span> km by end date)</p>
                    </div>

                    <div class="mb-3">
                        <h6>Special Cases</h6>
                        <p>If your estimated total exceeds your target, the probability starts at 90% and increases based on your current progress.</p>
                    </div>

                    <div class="alert alert-info">
                        <strong>Final Probability: <span id="finalProbabilityValue"></span>%</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
