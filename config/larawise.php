<?php

return [

    'cache'     => [
        'service'       => [
            'prefix'        => env('CACHE_SERVICE_PREFIX', 'larawise'),
            'ttl'           => (int) env('CACHE_SERVICE_TTL', 600),
            'track'         => (bool) env('CACHE_SERVICE_TRACKING', true),
            'tags'          => (bool) env('CACHE_SERVICE_TRACKING', false),
            'compress'      => (bool) env('CACHE_SERVICE_COMPRESS', false),
            'encrypt'       => (bool) env('CACHE_SERVICE_ENCRYPT', false),
            'sliding'       => (bool) env('CACHE_SERVICE_SLIDING', false),
        ]
    ]
];
