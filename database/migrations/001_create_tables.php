<?php

use Illuminate\Database\Capsule\Manager as Capsule;

// Create the runs table
Capsule::schema()->create('runs', function ($table) {
    $table->id();
    $table->date('date');
    $table->float('kilometers');
});

// Create the settings table
Capsule::schema()->create('settings', function ($table) {
    $table->id();
    $table->date('start_date');
    $table->date('end_date');
    $table->float('target_kilometers');
});
