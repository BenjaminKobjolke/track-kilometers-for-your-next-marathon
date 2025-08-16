<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class User extends Model
{
protected $fillable = [
        'email',
        'password',
        'remember_token',
        'token_expires_at',
        'reset_token',
        'reset_token_expires_at',
        'activation_token',
        'is_active',
        'last_active_session_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'reset_token'
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'reset_token_expires_at' => 'datetime'
    ];

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function lastActiveSession()
    {
        return $this->belongsTo(Session::class, 'last_active_session_id');
    }

    public function isRememberTokenValid()
    {
        if (!$this->remember_token || !$this->token_expires_at) {
            return false;
        }

        return $this->token_expires_at > Carbon::now();
    }

    public function generateRememberToken()
    {
        $this->remember_token = bin2hex(random_bytes(32));
        $this->token_expires_at = Carbon::now()->addDays(30);
        $this->save();
    }

    public function clearRememberToken()
    {
        $this->remember_token = null;
        $this->token_expires_at = null;
        $this->save();
    }

    public function generateResetToken()
    {
        $this->reset_token = bin2hex(random_bytes(32));
        $this->reset_token_expires_at = Carbon::now()->addDay();
        $this->save();
    }

    public function clearResetToken()
    {
        $this->reset_token = null;
        $this->reset_token_expires_at = null;
        $this->save();
    }

    public function isResetTokenValid()
    {
        if (!$this->reset_token || !$this->reset_token_expires_at) {
            return false;
        }

        return $this->reset_token_expires_at > Carbon::now();
    }

    public function activate()
    {
        $this->is_active = true;
        $this->activation_token = null;
        $this->save();
    }

    public function isActive()
    {
        return $this->is_active;
    }

    public function activateOnPasswordReset()
    {
        if (!$this->is_active) {
            $this->activate();
            return true;
        }
        return false;
    }
}
