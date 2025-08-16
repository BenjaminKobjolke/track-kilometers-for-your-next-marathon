<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';
    protected $fillable = ['theme', 'language'];

    // Disable Laravel's default timestamps
    public $timestamps = false;

    /**
     * Get default settings or create if not exists
     *
     * @return Settings
     */
    public static function getDefault(): Settings
    {
        $settings = self::first();
        if (!$settings) {
            $settings = self::create([
                'theme' => 'light',
                'language' => 'en'
            ]);
        }
        return $settings;
    }
}
