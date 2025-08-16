<?php

use Illuminate\Database\Capsule\Manager as Capsule;

return [
    'up' => function () {
        if (!Capsule::schema()->hasColumn('settings', 'language')) {
            Capsule::schema()->table('settings', function ($table) {
                $table->string('language')->default('en');
            });
        }
    },
    'down' => function () {
        if (Capsule::schema()->hasColumn('settings', 'language')) {
            Capsule::schema()->table('settings', function ($table) {
                $table->dropColumn('language');
            });
        }
    }
];
