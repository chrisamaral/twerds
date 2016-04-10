<?php

define('CONSUMER_KEY', getenv('CONSUMER_KEY'));
define('CONSUMER_SECRET', getenv('CONSUMER_SECRET'));
define('OAUTH_CALLBACK', getenv('OAUTH_CALLBACK'));
define('EXTENDED_ERROR', strtolower(getenv('DISPLAY_ERROR_DETAILS')) === 'true');
define('USE_CACHE', !empty(getenv('TEMPLATE_CACHE_DIR')));

return [
    'settings' => [
        'displayErrorDetails' => EXTENDED_ERROR,
        
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
            'options' => USE_CACHE ? [
                'cache' => getenv('TEMPLATE_CACHE_DIR')
            ] : []
        ],
    ],
];
