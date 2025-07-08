<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Run extends Model
{
    protected $table = 'runs';
    protected $fillable = ['date', 'kilometers', 'session_id'];

    // Disable Laravel's default timestamps
    public $timestamps = false;

    /**
     * Get the formatted date
     *
     * @return string
     */
    public function getFormattedDateAttribute(): string
    {
        $date = explode('-', $this->date);
        return $date[2] . '.' . $date[1] . '.' . $date[0];
    }

    /**
     * Calculate average kilometers per day between two dates
     *
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public static function calculateAverageKilometers(string $startDate, string $endDate, ?int $sessionId = null): float
    {
        $query = self::whereBetween('date', [$startDate, $endDate]);
        
        if ($sessionId !== null) {
            $query->where('session_id', $sessionId);
        }
        
        $runs = $query->get();
        $totalKilometers = $runs->sum('kilometers');
        $daysDifference = ceil((strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24)) + 1;
        
        return $daysDifference > 0 ? $totalKilometers / $daysDifference : 0;
    }
}
