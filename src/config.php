<?php

return [
    // The current environment. Set to "development" to enable debugging mode
    'env' => 'production',

    // This is the base site object and all other sites inherit from this
    'site' => [
        'metadata' => [
            'title' => 'Satellite',
        ],

        'locales' => [
            'available' => ['en'],
            'default' => 'en'
        ],
    ],

    'sites' => [
        'default' => [
            'domains' => ['/^(.*)$/'],
        ]
    ],

    'cache' => [
        // 'path' => __DIR__ . '/../../cache',
        'path' => false
    ],

    // See https://www.slimframework.com/docs/objects/application.html#application-configuration
    // for more details
    'system' => [
        'settings' => [
            'displayErrorDetails' => false,
        ]
    ]
];
