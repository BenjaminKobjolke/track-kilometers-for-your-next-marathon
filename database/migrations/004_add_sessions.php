<?php

use Illuminate\Database\Capsule\Manager as Capsule;

return [
    'up' => function () {
        // Create sessions table if it doesn't exist
        if (!Capsule::schema()->hasTable('sessions')) {
            Capsule::schema()->create('sessions', function ($table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('name');
                $table->date('start_date');
                $table->date('end_date');
                $table->string('status', 10)->default('active'); // Limit to 10 chars, enough for 'completed'
                $table->timestamps();
            });
        }

        // Add session_id to runs table if it doesn't exist
        if (!Capsule::schema()->hasColumn('runs', 'session_id')) {
            Capsule::schema()->table('runs', function ($table) {
                $table->foreignId('session_id')->nullable()->constrained('sessions')->onDelete('cascade');
            });
        }
    },
    'down' => function () {
        // Remove session_id from runs table
        Capsule::schema()->table('runs', function ($table) {
            $table->dropForeign(['session_id']);
            $table->dropColumn('session_id');
        });

        // Drop sessions table
        Capsule::schema()->dropIfExists('sessions');
    }
];
