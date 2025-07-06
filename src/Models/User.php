<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['email', 'password'];
    protected $hidden = ['password', 'remember_token', 'reset_token'];

    /**
     * Generate a remember me token
     */
    public function generateRememberToken(): void
    {
        $this->remember_token = bin2hex(random_bytes(32));
        $this->token_expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
        $this->save();
    }

    /**
     * Generate a password reset token
     */
    public function generateResetToken(): void
    {
        $this->reset_token = bin2hex(random_bytes(32));
        $this->reset_token_expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $this->save();
    }

    /**
     * Check if remember token is valid
     */
    public function isRememberTokenValid(): bool
    {
        if (!$this->remember_token || !$this->token_expires_at) {
            return false;
        }

        return strtotime($this->token_expires_at) > time();
    }

    /**
     * Check if reset token is valid
     */
    public function isResetTokenValid(): bool
    {
        if (!$this->reset_token || !$this->reset_token_expires_at) {
            return false;
        }

        return strtotime($this->reset_token_expires_at) > time();
    }

    /**
     * Clear remember token
     */
    public function clearRememberToken(): void
    {
        $this->remember_token = null;
        $this->token_expires_at = null;
        $this->save();
    }

    /**
     * Clear reset token
     */
    public function clearResetToken(): void
    {
        $this->reset_token = null;
        $this->reset_token_expires_at = null;
        $this->save();
    }
}
