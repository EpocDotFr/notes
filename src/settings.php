<?php
return [
    'settings' => [
        'displayErrorDetails' => true,

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/'
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],
        
        // Kanboard settings
        'kanboard' => [
            // Absolute URL to the Kanboard's jsonrpc.php file (can be found in Kanboard in Preferences > API)
            'endpoint' => 'http://tasks.website.com/jsonrpc.php',
            // The token to access the JSON-RPC API (can be found in Kanboard in Preferences > API as well)
            'token' => 'XXXXXXXXX',
            // The project that will store our notes
            'project_id' => 20,
            // The column that will store our notes (I think you can define to 0 to use the default column)
            'column_id' => 88,
            // The swimlane that will store our notes (0: default swimlane)
            'swimlane_id' => 0,
            // Don't touch this
            'allowed_columns' => ['id', 'title', 'description', 'color_id', 'url', 'date_due']
        ]
    ],
];
