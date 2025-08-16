<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'start_date',
        'end_date',
        'status',
        'target_kilometers',
        'unit',
        'unit_short'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'string',
        'target_kilometers' => 'float',
        'unit' => 'string',
        'unit_short' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function runs()
    {
        return $this->hasMany(Run::class);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function complete()
    {
        $this->status = 'completed';
        $this->save();
    }
}
