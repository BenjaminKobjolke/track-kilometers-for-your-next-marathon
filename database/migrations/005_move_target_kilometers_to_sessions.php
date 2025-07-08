<?php

use Illuminate\Database\Capsule\Manager as Capsule;

return [
    'up' => function () {
        // Add target_kilometers to sessions table
        Capsule::schema()->table('sessions', function ($table) {
            $table->decimal('target_kilometers', 8, 1)->default(500);
        });

        // Copy default target_kilometers from settings to existing sessions
        $defaultSettings = Capsule::table('settings')->first();
        if ($defaultSettings) {
            $defaultTarget = $defaultSettings->target_kilometers;
            Capsule::table('sessions')->update(['target_kilometers' => $defaultTarget]);
        }

        // Remove target_kilometers from settings table if it exists
        if (Capsule::schema()->hasColumn('settings', 'target_kilometers')) {
            Capsule::schema()->table('settings', function ($table) {
                $table->dropColumn('target_kilometers');
            });
        }
    },
    'down' => function () {
        // Add target_kilometers back to settings table
        Capsule::schema()->table('settings', function ($table) {
            $table->decimal('target_kilometers', 8, 1)->default(500);
        });

        // Copy target_kilometers from first active session (if exists) to settings
        $firstSession = Capsule::table('sessions')->where('status', 'active')->first();
        if ($firstSession) {
            Capsule::table('settings')->update(['target_kilometers' => $firstSession->target_kilometers]);
        }

        // Remove target_kilometers from sessions table
        Capsule::schema()->table('sessions', function ($table) {
            $table->dropColumn('target_kilometers');
        });
    }
];
