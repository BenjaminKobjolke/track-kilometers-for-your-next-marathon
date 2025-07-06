<?php

require_once __DIR__ . '/../bootstrap.php';

try {
    // Drop existing tables if they exist
    $capsule->schema()->dropIfExists('runs');
    $capsule->schema()->dropIfExists('settings');
    $capsule->schema()->dropIfExists('users');

    // Create the runs table
    $capsule->schema()->create('runs', function ($table) {
        $table->id();
        $table->date('date');
        $table->float('kilometers');
    });

    // Create the settings table
    $capsule->schema()->create('settings', function ($table) {
        $table->id();
        $table->date('start_date');
        $table->date('end_date');
        $table->float('target_kilometers');
        $table->string('theme')->default('light');
    });

    // Create the users table
    $capsule->schema()->create('users', function ($table) {
        $table->id();
        $table->string('email')->unique();
        $table->string('password');
        $table->string('remember_token')->nullable();
        $table->timestamp('token_expires_at')->nullable();
        $table->string('reset_token')->nullable();
        $table->timestamp('reset_token_expires_at')->nullable();
        $table->timestamps();
    });

    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
