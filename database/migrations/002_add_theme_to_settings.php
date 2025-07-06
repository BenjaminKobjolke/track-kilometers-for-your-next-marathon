<?php

use Illuminate\Database\Capsule\Manager as Capsule;

// Add theme column to settings table
Capsule::schema()->table('settings', function ($table) {
    $table->string('theme')->default('light');
});
