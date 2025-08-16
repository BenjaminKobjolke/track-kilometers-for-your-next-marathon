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

    public function get($key, array $params = [])
    {
        if (!isset($this->translations[$key])) {
            return $key;
        }

        $translation = $this->translations[$key];

        // Replace parameters
        foreach ($params as $param => $value) {
            $translation = str_replace("{{$param}}", $value, $translation);
        }

        return $translation;
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
