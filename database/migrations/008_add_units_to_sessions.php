<?php

use Illuminate\Database\Capsule\Manager as Capsule;

return [
    'up' => function () {
        // Check if unit columns don't exist before adding them
        if (!Capsule::schema()->hasColumn('sessions', 'unit')) {
            Capsule::schema()->table('sessions', function ($table) {
                $table->string('unit')->default('Kilometers');
                $table->string('unit_short')->default('km');
            });
        }

        // Check if we need to rename kilometers to amount in runs table
        if (Capsule::schema()->hasColumn('runs', 'kilometers') && !Capsule::schema()->hasColumn('runs', 'amount')) {
            // SQLite doesn't support renaming columns directly, so we'll add the new column and copy data
            Capsule::schema()->table('runs', function ($table) {
                $table->float('amount');
            });
            
            // Copy data from kilometers to amount
            Capsule::statement('UPDATE runs SET amount = kilometers');
            
            // Drop the old kilometers column
            Capsule::schema()->table('runs', function ($table) {
                $table->dropColumn('kilometers');
            });
        }
    },
    'down' => function () {
        // Remove unit fields from sessions table
        if (Capsule::schema()->hasColumn('sessions', 'unit')) {
            Capsule::schema()->table('sessions', function ($table) {
                $table->dropColumn(['unit', 'unit_short']);
            });
        }

        // Rename amount column back to kilometers in runs table
        if (Capsule::schema()->hasColumn('runs', 'amount') && !Capsule::schema()->hasColumn('runs', 'kilometers')) {
            Capsule::schema()->table('runs', function ($table) {
                $table->float('kilometers');
            });
            
            // Copy data from amount to kilometers
            Capsule::statement('UPDATE runs SET kilometers = amount');
            
            // Drop the new amount column
            Capsule::schema()->table('runs', function ($table) {
                $table->dropColumn('amount');
            });
        }
    }
];