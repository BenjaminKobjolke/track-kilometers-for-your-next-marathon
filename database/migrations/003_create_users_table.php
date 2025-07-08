<?php

use Illuminate\Database\Capsule\Manager as Capsule;

return [
    'up' => function () {
        // Skip if users table already exists
        if (Capsule::schema()->hasTable('users')) {
            return;
        }

        // Create users table
        Capsule::schema()->create('users', function ($table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('reset_token')->nullable();
            $table->timestamp('reset_token_expires_at')->nullable();
            $table->timestamps();
        });
    },
    'down' => function () {
        Capsule::schema()->dropIfExists('users');
    }
];
