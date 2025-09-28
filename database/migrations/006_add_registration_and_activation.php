<?php

use Illuminate\Database\Capsule\Manager as Capsule;

return [
    'up' => function () {
        // Create register_log table if it doesn't exist
        if (!Capsule::schema()->hasTable('register_log')) {
            Capsule::schema()->create('register_log', function ($table) {
                $table->id();
                $table->string('ip_hash');
                $table->timestamps();
            });
        }

        // Add activation columns to users table if they don't exist
        if (!Capsule::schema()->hasColumn('users', 'activation_token')) {
            Capsule::schema()->table('users', function ($table) {
                $table->string('activation_token')->nullable();
            });
        }

        if (!Capsule::schema()->hasColumn('users', 'is_active')) {
            Capsule::schema()->table('users', function ($table) {
                $table->boolean('is_active')->default(false);
            });
        }
    },
    'down' => function () {
        Capsule::schema()->dropIfExists('register_log');
        
        Capsule::schema()->table('users', function ($table) {
            $table->dropColumn(['activation_token', 'is_active']);
        });
    }
];
