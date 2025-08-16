<?php

namespace Controllers;

use Models\Session;
use Models\Settings;

class SessionController {
    private $userId;

    public function __construct() {
        $this->userId = $_SESSION['user_id'] ?? null;
    }

    public function getSettings() {
        return Settings::getDefault();
    }

    private function checkAuth() {
        if (!$this->userId) {
            throw new \Exception('User not authenticated');
        }
    }

    public function getActiveSession() {
        $this->checkAuth();
        $activeSession = null;

        // Try to get session from session ID
        if (isset($_SESSION['active_session_id'])) {
            $activeSession = Session::where('id', $_SESSION['active_session_id'])
                ->where('user_id', $this->userId)
                ->where('status', '=', 'active')
                ->first();
            
            // Clear session ID if session is not found or not active
            if (!$activeSession) {
                unset($_SESSION['active_session_id']);
            }
        }

        // If no active session, try to find the most recent active session
        if (!$activeSession) {
            $activeSession = Session::where('user_id', $this->userId)
                ->where('status', '=', 'active')
                ->orderBy('created_at', 'desc')
                ->first();
            
            // Store the session ID if found
            if ($activeSession) {
                $_SESSION['active_session_id'] = $activeSession->id;
            }
        }

        return $activeSession;
    }
}
