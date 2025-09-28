<?php

use Illuminate\Database\Capsule\Manager as Capsule;

return [
    'up' => function () {
        // Create the runs table if it doesn't exist
        if (!Capsule::schema()->hasTable('runs')) {
            Capsule::schema()->create('runs', function ($table) {
                $table->id();
                $table->date('date');
                $table->float('kilometers');
            });
        }

        // Create the settings table if it doesn't exist
        if (!Capsule::schema()->hasTable('settings')) {
            Capsule::schema()->create('settings', function ($table) {
                $table->id();
                $table->string('theme')->default('light');
            });
        }
    },
    'down' => function () {
        Capsule::schema()->dropIfExists('settings');
        Capsule::schema()->dropIfExists('runs');
    }
];
