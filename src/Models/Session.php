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
        'target_kilometers'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'string',
        'target_kilometers' => 'float'
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
