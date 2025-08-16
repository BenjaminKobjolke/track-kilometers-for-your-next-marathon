<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Run extends Model
{
    protected $table = 'runs';
    protected $fillable = ['date', 'amount', 'session_id'];

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

    public static function calculateAverageAmount(string $startDate, string $endDate, ?int $sessionId = null): float
    {
        $query = self::whereBetween('date', [$startDate, $endDate]);
        
        if ($sessionId !== null) {
            $query->where('session_id', $sessionId);
        }
        
        $runs = $query->get();
        $totalAmount = $runs->sum('amount');
        $daysDifference = ceil((strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24)) + 1;
        
        return $daysDifference > 0 ? $totalAmount / $daysDifference : 0;
    }

    // Keep old method name for backwards compatibility
    public static function calculateAverageKilometers(string $startDate, string $endDate, ?int $sessionId = null): float
    {
        return self::calculateAverageAmount($startDate, $endDate, $sessionId);
    }
}
