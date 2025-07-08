<?php

use Illuminate\Database\Capsule\Manager as Capsule;

return [
    'up' => function () {
        // Create register_log table
        Capsule::schema()->create('register_log', function ($table) {
            $table->id();
            $table->string('ip_hash');
            $table->timestamps();
        });

        // Add activation columns to users table
        Capsule::schema()->table('users', function ($table) {
            $table->string('activation_token')->nullable();
            $table->boolean('is_active')->default(false);
        });
    },
    'down' => function () {
        Capsule::schema()->dropIfExists('register_log');
        
        Capsule::schema()->table('users', function ($table) {
            $table->dropColumn(['activation_token', 'is_active']);
        });
    }
];
