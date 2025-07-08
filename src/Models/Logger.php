<?php

namespace Models;

class Logger {
    private $logFile;
    
    public function __construct($type = 'app') {
        $dir = __DIR__ . '/../../logs';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $this->logFile = $dir . '/' . $type . '.log';
        
        // Create log file if it doesn't exist and set permissions
        if (!file_exists($this->logFile)) {
            touch($this->logFile);
            chmod($this->logFile, 0777);
        }
    }
    
    public function error($message, $context = []) {
        $this->log('ERROR', $message, $context);
    }
    
    public function info($message, $context = []) {
        $this->log('INFO', $message, $context);
    }
    
    public function debug($message, $context = []) {
        $this->log('DEBUG', $message, $context);
    }
    
    private function log($level, $message, $context) {
        $date = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_PRETTY_PRINT) : '';
        $entry = "[$date] $level: $message$contextStr\n";
        
        if (!is_writable($this->logFile)) {
            // Try to make the file writable
            chmod($this->logFile, 0777);
            if (!is_writable($this->logFile)) {
                // If still not writable, throw an exception
                throw new \Exception("Log file not writable: " . $this->logFile);
            }
        }
        
        file_put_contents($this->logFile, $entry, FILE_APPEND);
    }
    
    public function getLogPath() {
        return $this->logFile;
    }
}
