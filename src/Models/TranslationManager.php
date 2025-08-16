<?php

namespace Models;

class TranslationManager
{
    private $translations = [];
    private $lang = 'en';

    public function __construct($lang = 'en')
    {
        $this->lang = $lang;
        $this->loadTranslations();
    }

    private function loadTranslations()
    {
        $path = dirname(dirname(__DIR__)) . "/public/data/lang/{$this->lang}.json";
        if (file_exists($path)) {
            $this->translations = json_decode(file_get_contents($path), true);
        }
    }

    public function get($key, array $params = [], $session = null)
    {
        if (!isset($this->translations[$key])) {
            return $key;
        }

        $translation = $this->translations[$key];

        // Auto-inject session unit data with fallbacks
        $defaultParams = [
            'unit' => 'Entry',
            'unit_short' => 'entries'
        ];

        if ($session && isset($session->unit) && isset($session->unit_short)) {
            $defaultParams = [
                'unit' => $session->unit,
                'unit_short' => $session->unit_short
            ];
        }

        $params = array_merge($defaultParams, $params);

        // Replace parameters
        foreach ($params as $param => $value) {
            $translation = str_replace("{{$param}}", $value, $translation);
        }

        return $translation;
    }

    // Helper method for getting translations with automatic session context
    public function getWithSession($key, $session, array $params = [])
    {
        return $this->get($key, $params, $session);
    }

    public function getAll()
    {
        return $this->translations;
    }

    public function getCurrentLanguage()
    {
        return $this->lang;
    }
}
