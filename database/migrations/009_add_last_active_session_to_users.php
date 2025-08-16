<?php

use Illuminate\Database\Capsule\Manager as Capsule;

return [
    'up' => function () {
        // Check if last_active_session_id column doesn't exist before adding it
        if (!Capsule::schema()->hasColumn('users', 'last_active_session_id')) {
            Capsule::schema()->table('users', function ($table) {
                $table->integer('last_active_session_id')->nullable();
                $table->foreign('last_active_session_id')->references('id')->on('sessions')->onDelete('set null');
            });
        }
    },
    'down' => function () {
        // Remove last_active_session_id field from users table
        if (Capsule::schema()->hasColumn('users', 'last_active_session_id')) {
            Capsule::schema()->table('users', function ($table) {
                $table->dropForeign(['last_active_session_id']);
                $table->dropColumn('last_active_session_id');
            });
        }
    }
];