<?php
return [
    'settings' => [
        // Slim Settings
        'debug' => true,
        'mode' => 'development',
        'displayErrorDetails' => true,
        'determineRouteBeforeAppMiddleware' => true,

        // monolog settings
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/../log/app.log',
        ],
    ],
];
