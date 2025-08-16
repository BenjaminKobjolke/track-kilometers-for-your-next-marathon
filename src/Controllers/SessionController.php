<?php

namespace Controllers;

use Models\Session;
use Models\Settings;
use Models\User;

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

        // If no active session, try to get the user's last active session
        if (!$activeSession) {
            $user = User::find($this->userId);
            if ($user && $user->last_active_session_id) {
                $activeSession = Session::where('id', $user->last_active_session_id)
                    ->where('user_id', $this->userId)
                    ->where('status', '=', 'active')
                    ->first();
                
                if ($activeSession) {
                    $_SESSION['active_session_id'] = $activeSession->id;
                }
            }
        }

        // If still no active session, try to find the most recent active session
        if (!$activeSession) {
            $activeSession = Session::where('user_id', $this->userId)
                ->where('status', '=', 'active')
                ->orderBy('created_at', 'desc')
                ->first();
            
            // Store the session ID if found
            if ($activeSession) {
                $_SESSION['active_session_id'] = $activeSession->id;
                $this->updateLastActiveSession($activeSession->id);
            }
        }

        return $activeSession;
    }

    public function setActiveSession($sessionId) {
        $this->checkAuth();
        
        $session = Session::where('id', $sessionId)
            ->where('user_id', $this->userId)
            ->where('status', '=', 'active')
            ->first();
        
        if ($session) {
            $_SESSION['active_session_id'] = $session->id;
            $this->updateLastActiveSession($session->id);
            return $session;
        }
        
        throw new \Exception('Session not found or not active');
    }

    private function updateLastActiveSession($sessionId) {
        $user = User::find($this->userId);
        if ($user) {
            $user->last_active_session_id = $sessionId;
            $user->save();
        }
    }
}
