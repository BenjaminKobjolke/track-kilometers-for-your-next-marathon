<?php

use Illuminate\Database\Capsule\Manager as Capsule;

return [
    'up' => function () {
        // Drop existing tables if they exist
        Capsule::schema()->dropIfExists('runs');
        Capsule::schema()->dropIfExists('settings');

        // Create the runs table
        Capsule::schema()->create('runs', function ($table) {
            $table->id();
            $table->date('date');
            $table->float('kilometers');
        });

        // Create the settings table
        Capsule::schema()->create('settings', function ($table) {
            $table->id();
            $table->string('theme')->default('light');
        });
    },
    'down' => function () {
        Capsule::schema()->dropIfExists('settings');
        Capsule::schema()->dropIfExists('runs');
    }
];
