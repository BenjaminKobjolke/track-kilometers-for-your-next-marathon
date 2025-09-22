<?php
return [
    'base_url' => '/track-kilometers-for-your-next-marathon/public',  // Change this for different environments
    'database' => [
        'path' => __DIR__ . '/data/database.sqlite'  // Database file location (outside git)
    ],
    'email' => [
        'from_address' => 'tracker@your-server.com',
        'from_name' => 'Training Tracker'
    ]
];
