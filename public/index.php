<?php

// Start session first
session_start();

require_once __DIR__ . '/../bootstrap.php';
$config = require __DIR__ . '/../config.php';

use Controllers\AuthController;
use Controllers\SessionController;
use Controllers\StatsController;
use Models\TranslationManager;

// Initialize controllers
$sessionController = new SessionController();
$authController = new AuthController();
$statsController = new StatsController();

// Get settings and initialize translator (no auth needed)
$settings = $sessionController->getSettings();
$translator = new TranslationManager($settings->language);

// Handle authentication
$authController->checkAuth();

// Get session data (requires auth)
$activeSession = $sessionController->getActiveSession();
$stats = $statsController->calculateStats($activeSession);

// Include view components
require_once __DIR__ . '/../src/Views/layout/header.php';
require_once __DIR__ . '/../src/Views/layout/navigation.php';
require_once __DIR__ . '/../src/Views/components/stats-panel.php';
require_once __DIR__ . '/../src/Views/components/runs-table.php';

// Include modals
require_once __DIR__ . '/../src/Views/modals/settings.php';
require_once __DIR__ . '/../src/Views/modals/run-form.php';
require_once __DIR__ . '/../src/Views/modals/create-session.php';
require_once __DIR__ . '/../src/Views/modals/select-session.php';
require_once __DIR__ . '/../src/Views/modals/past-sessions.php';
require_once __DIR__ . '/../src/Views/modals/probability-info.php';

require_once __DIR__ . '/../src/Views/layout/footer.php';
