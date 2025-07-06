<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';
    protected $fillable = ['start_date', 'end_date', 'target_kilometers', 'theme'];

    // Disable Laravel's default timestamps
    public $timestamps = false;

    /**
     * Get the remaining days until end date
     *
     * @return int
     */
    public function getRemainingDays(): int
    {
        $now = time();
        $endDate = strtotime($this->end_date);
        $daysRemaining = ceil(($endDate - $now) / (60 * 60 * 24));
        return max(0, $daysRemaining);
    }

    /**
     * Calculate estimated total kilometers by end date
     *
     * @return float
     */
    public function getEstimatedTotalKilometers(): float
    {
        $averageKilometers = Run::calculateAverageKilometers($this->start_date, date('Y-m-d'));
        $totalDays = $this->getRemainingDays() + ceil((time() - strtotime($this->start_date)) / (60 * 60 * 24));
        return $averageKilometers * $totalDays;
    }

    /**
     * Calculate probability of reaching target
     *
     * @return float
     */
    public function getTargetProbability(): float
    {
        $estimatedTotal = $this->getEstimatedTotalKilometers();
        $probability = ($estimatedTotal / $this->target_kilometers) * 100;
        return min(100, max(0, $probability));
    }

    /**
     * Get default settings or create if not exists
     *
     * @return Settings
     */
    /**
     * Get the formatted start date
     *
     * @return string
     */
    public function getFormattedStartDateAttribute(): string
    {
        $date = explode('-', $this->start_date);
        return $date[2] . '.' . $date[1] . '.' . $date[0];
    }

    /**
     * Get the formatted end date
     *
     * @return string
     */
    public function getFormattedEndDateAttribute(): string
    {
        $date = explode('-', $this->end_date);
        return $date[2] . '.' . $date[1] . '.' . $date[0];
    }

    public static function getDefault(): Settings
    {
        $settings = self::first();
        if (!$settings) {
            $settings = self::create([
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+3 months')),
                'target_kilometers' => 500,
                'theme' => 'light'
            ]);
        }
        return $settings;
    }
}
