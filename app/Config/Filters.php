<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
    public $aliases = [
        'api' => \App\Filters\ApiFilter::class,
        'auth' => \App\Filters\AuthFilter::class,
        'cors' => \App\Filters\CorsFilter::class,
        'secureheaders' => \App\Filters\SecureHeadersFilter::class,
    ];

    public $globals = [
        'before' => [
            'api',
            'secureheaders', // Security headers for all requests
            'cors',          // CORS handling
            'auth' => [
                'except' => [
                    'api/login', 
                    'api/register',
                    'api/users', 'api/users/*',
                    'api/sliders', 'api/sliders/*',
                    'api/sponsors', 'api/sponsors/*',
                    'api/contacts', 'api/contacts/*',
                    'api/clients', 'api/clients/*',
                    'api/projects', 'api/projects/*',
                    'api/blogs', 'api/blogs/*',
                    'api/certifications', 'api/certifications/*'
                ]
            ],
        ],
        'after' => [
            'cors', // Ensure CORS headers in response
        ]
    ];

    public $methods = [
        'post' => [ 'cors'], // CSRF protection for POST
    ];
}